<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

$_PLUGIN_ROOT = dirname(dirname(__file__));
require_once($_PLUGIN_ROOT . DIRECTORY_SEPARATOR . 'kintassa_core/kin_micro_orm.php');
require_once($_PLUGIN_ROOT . DIRECTORY_SEPARATOR . 'src/kgal_image.php');
require_once($_PLUGIN_ROOT . DIRECTORY_SEPARATOR . "src/kgal_gallery_applet.php");

class KintassaPagerSlideshowApplet extends KintassaGalleryApplet {
	static function register() {
		KintassaGalleryApplet::register('KintassaPagerSlideshowApplet', 'pager_slideshow', "Pager Slideshow");
	}

	function classes() {
		$cls = parent::classes();
		$cls[] = "kintassa-pager-slideshow-applet";
		return $cls;
	}

	function render() {
		$applet = $this;

		$gallery = $this->gallery;
		$unique_id = $this->unique_id();
		$cls = $this->classes_attrib_str();
		$sty = $this->styles_attrib_str();

		$template = $this->template_path("pager_slideshow", "render");
		require($template);
	}
}

KintassaPagerSlideshowApplet::register();

?>