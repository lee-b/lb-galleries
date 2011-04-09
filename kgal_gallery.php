<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kin_micro_orm.php');
require_once('kin_applet.php');

abstract class KintassaGalleryApp extends KintassaApplet {
	function __construct($gallery) {
		parent::__construct();
		$this->gallery = $gallery;
	}
}

class KintassaAutomatedSlideshowGalleryApp extends KintassaGalleryApp {
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

		$gallery_code .= ">GALLERY NUMBER {$gallery->id}</div>";

		echo($gallery_code);
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
	function __construct($id = null) {
		parent::__construct($id);

		if (!$this->is_loaded()) {
			$this->load($id);
		}
	}

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

	function load($id) {
		global $wpdb;

		assert ($id != null);
		$row = $wpdb->get_row("SELECT * FROM `{$this->table_name()}` WHERE id={$id}");

		$this->name = $row->name;
		$this->width = $row->width;
		$this->height = $row->height;
		$this->display_mode = $row->display_mode;
		$this->id = $id;
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

		$rows = $wpdb->get_results("SELECT id,sort_pri FROM `{KintassaGalleryImage::table_name()}` WHERE gallery_id={$this->id} ORDER BY sort_pri,title");

		$images = array();
		foreach ($rows as $row) {
			$img = new KintassaGalleryImage($row->id);
			$images.add($img);
		}

		return $images;
	}
}

?>