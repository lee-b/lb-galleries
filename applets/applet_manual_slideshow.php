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

class KintassaManualSlideshowApplet extends KintassaGalleryApplet {
	static function register() {
		KintassaGalleryApplet::register('KintassaManualSlideshowApplet', 'manual_slideshow', "Manual Slideshow");
	}

	function classes($suffix=null) {
		$cls = parent::classes($suffix);
		$cls[] = "kintassa-manual-slideshow-applet$suffix";
		return $cls;
	}

	function render() {
		$applet = $this;

		$gallery = $this->gallery;
		$unique_id = $this->unique_id();
		$cls = $this->classes_attrib_str();
		$wrapper_cls = $this->wrapper_classes_attrib_str();
		$sty = $this->styles_attrib_str();
		$companion_sty = $this->companion_styles_attrib_str();

		$template = $this->template_path("manual_slideshow", "render");

		ob_start();
		require($template);
		$template_html = ob_get_contents();
		ob_end_clean();

		return $template_html;
	}
}

KintassaManualSlideshowApplet::register();

?>