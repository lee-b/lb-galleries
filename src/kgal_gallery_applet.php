<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once('kgal_config.php');
require_once('kgal_image_finder.php');
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . 'kintassa_core/kin_applet.php');

$GLOBALS['registered_kintassa_gallery_applets'] = array();

abstract class KintassaGalleryApplet extends KintassaApplet {
	static function register($applet_class, $name, $pretty_name) {
		if (array_key_exists($applet_class, $GLOBALS['registered_kintassa_gallery_applets'])) return;
		$GLOBALS['registered_kintassa_gallery_applets'][$name] = array(
			'class'			=> $applet_class,
			'pretty_name'	=> $pretty_name,
		);
	}

	static function available_applets() {
		return array_keys($GLOBALS['registered_kintassa_gallery_applets']);
	}

	static function is_valid_applet($applet_name) {
		return array_key_exists($applet_name, $GLOBALS['registered_kintassa_gallery_applets']);
	}

	static function applet_info($applet_name) {
		return $GLOBALS['registered_kintassa_gallery_applets'][$applet_name];
	}

	function __construct($gallery, $width=null, $height=null) {
		parent::__construct();
		$this->gallery = $gallery;
		$this->width = $width;
		$this->height = $height;
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

	function width() {
		if ($this->width != null) {
			$w = $this->width;
		} else {
			$w = $this->gallery->width;
		}
		return $w;
	}

	function height() {
		if ($this->height != null) {
			$h = $this->height;
		} else {
			$h = $this->gallery->height;
		}

		return $h;
	}

	function styles() {
		$sty = array();
		$sty['width'] = $this->width() . "px";
		$sty['height'] = $this->height() . "px";
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

?>