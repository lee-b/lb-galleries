<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kgal_config.php');
require_once('kin_micro_orm.php');
require_once('kin_applet.php');
require_once('kgal_image.php');
require_once('kgal_image_finder.php');


abstract class KintassaGalleryApp extends KintassaApplet {
	function __construct($gallery) {
		parent::__construct();
		$this->gallery = $gallery;
		$this->finder = new KGalImageFinder(KGAL_CACHE_PATH);
	}

	function unique_id() {
		return "kintassa-gallery-{$this->gallery->id}";
	}

	function image_uri($img) {
		return $this->finder->uri_from_id($img->id);
	}

	function classes() {
		return array("kintassa-gallery-app");
	}

	function classes_attrib_str() {
		return "class=\"" . implode(" ", $this->classes()) . "\"";
	}

	function styles() {
		$sty = array();
		$sty['width'] = $this->gallery->width;
		$sty['height'] = $this->gallery->height;
		return $sty;
	}

	function styles_attrib_str() {
		$style_str = "style=\"";
		$styles = $this->styles();
		foreach($styles as $k => $v) {
			$style_str .= "{$k}: {$v};";
		}
		$style_str .= "\"";
		return $style_str;
	}
}

/***
 * Gallery display applet using jQuery + Gallerific
 */
class KintassaAutomatedSlideshowGalleryApp extends KintassaGalleryApp {
	function classes() {
		$cls = parent::classes();
		$cls[] = "kintassa-automated-slideshow-app";
		return $cls;
	}

	function render_script($target) {
		echo(<<<HTML
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('{$target}').cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});
});</script>
HTML
);
	}

	function render() {
		$gallery = $this->gallery;
		$unique_id = $this->unique_id();
		$cls = $this->classes_attrib_str();
		$sty = $this->styles_attrib_str();

		$gallery_code = "<div id=\"$unique_id\" {$cls} {$sty}>";

		$images = $gallery->images();
		$first = true;
		foreach($images as $img) {
			if ($first) {
				$cls = " class=\"first-item\"";
				$first = false;
			} else {
				$cls = "";
			}

			$gallery_code .= "<img {$cls} width=\"{$gallery->width}\" height=\"{$gallery->height}\" src=\"" . $this->image_uri($img) . "\" title=\"{$img->name}\">";
		}
		$gallery_code .= "</div>";

		echo($gallery_code);
		$this->render_script("#{$unique_id}");
	}
}

class KintassaManualSlideshowGalleryApp extends KintassaGalleryApp {
	function render() {
		$gallery = $this->gallery;
		$gallery_code = "<div class=\"kintassa_gallery\"";

		$style_code = "";
		if ($gallery->width) {
			$style_code .= "width: {$gallery->width};";
		}

		if ($gallery->height) {
			$style_code .= "height: {$gallery->height};";
		}

		if (strlen($style_code) > 0) {
			$gallery_code .= " style=\"{$style_code}\"";
		}

		$gallery_code .= ">GALLERY NUMBER {$gallery->id}";

		$gallery_code .= "<div class=\"kintassa_slideshow_navbar\">(navbar)</div>";

		$gallery_code .= "</div>";

		echo($gallery_code);
	}
}

class KintassaGallery extends KintassaMicroORMObject {
	static function table_name() {
		global $wpdb;
		return $wpdb->prefix . "kintassa_gallery";
	}

	function save() {
		global $wpdb;

		if (!ISSET($this->id)) {
			// saving for the first time, so we need to insert a record
			$dat = array("name" => $this->name, "width" => $this->width, "height" => $this->height);
			$dat_fmt = array('%s', '%d', '%d');
			$res = $wpdb->insert($this->table_name, &$dat, &$dat_fmt);
			$this->id = $wpdb->insert_id;
		} else {
			$dat = array("name" => $this->name, "width" => $this->width, "height" => $this->height, "id" => $this->id);
			$dat_fmt = array('%s', '%d', '%d', '%d');
			$where = array("id" => $this->id);
			$res = $wpdb->update($this->table_name, &$dat, &$where, &$dat_fmt);
		}
	}

	function init() {
		$this->name = null;
		$this->width = null;
		$this->height = null;
		$this->display_mode = null;
	}

	function load() {
		global $wpdb;

		assert ($this->id != null);

		$row = $wpdb->get_row("SELECT * FROM `{$this->table_name()}` WHERE id={$this->id}");
		if (!$row) {
			return false;
		}

		$this->name = $row->name;
		$this->width = $row->width;
		$this->height = $row->height;
		$this->display_mode = $row->display_mode;

		return true;
	}

	function display_mode_map() {
		return array(
			"slideshow" => "KintassaAutomatedSlideshowGalleryApp",
			"manual_slideshow" => "KintassaManualSlideshowGalleryApp"
		);
	}

	function render($width = null, $height = null) {
		assert($this->id != null);

		$mode_map = $this->display_mode_map();

		assert (array_key_exists($this->display_mode, $mode_map));

		$app_class = $mode_map[$this->display_mode];

		$app = new $app_class($this);
		$app->render();
	}

	function images() {
		global $wpdb;

		require_once("kgal_image.php");

		$table_name = KintassaGalleryImage::table_name();
		$rows = $wpdb->get_results("SELECT id,sort_pri FROM `{$table_name}` WHERE gallery_id={$this->id} ORDER BY sort_pri,name");

		$images = array();
		foreach ($rows as $row) {
			$img = new KintassaGalleryImage($row->id);
			$images[] = $img;
		}

		return $images;
	}
}

?>