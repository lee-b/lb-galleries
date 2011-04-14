<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

$_PLUGIN_ROOT = dirname(dirname(__file__));
require_once($_PLUGIN_ROOT . DIRECTORY_SEPARATOR . "src/kgal_gallery_applet.php");

/***
 * Dummy renderer used for error messages when the requested renderer
 * doesn't exist
 */
class KintassaInvalidGalleryApplet extends KintassaGalleryApplet {
	static function register() {
		KintassaGalleryApplet::register('KintassaInvalidGalleryApplet', 'invalid', null);
	}

	function classes() {
		$cls = parent::classes();
		$cls[] = "kintassa-applet-invalid";
		return $cls;
	}

	function render() {
		$gallery = $this->gallery;

		$unique_id = $this->unique_id();

		$cls = $this->classes_attrib_str();
		$sty = $this->styles_attrib_str();

		$not_avail_msg = __("This gallery's renderer app is not available. Please (re)install the necessary sub-features, or change the gallery's Display Mode.");

		$template = $this->template_path("invalid", "render");
		require($template);
	}
}

KintassaInvalidGalleryApplet::register();

?>