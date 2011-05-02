<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_micro_orm.php'));
require_once(kin_gal_inc('kgal_image.php'));
require_once(kin_gal_inc('kgal_gallery_applet.php'));

/***
 * Gallery display applet using jQuery + Gallerific
 */
class KintassaSlideshowApplet extends KintassaGalleryApplet {
	static function register() {
		KintassaGalleryApplet::register('KintassaSlideshowApplet', 'slideshow', "Slideshow");
	}

	function classes() {
		$cls = parent::classes();
		$cls[] = "kintassa-applet-slideshow";
		return $cls;
	}

	function render() {
		$applet = $this;

		$gallery = $this->gallery;
		$unique_id = $this->unique_id();
		$cls = $this->classes_attrib_str();
		$sty = $this->styles_attrib_str();

		$template = $this->template_path("slideshow", "render");
		require($template);
	}
}

KintassaSlideshowApplet::register();

?>