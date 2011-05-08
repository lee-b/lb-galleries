<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kin_gal_inc('kgal_gallery_applet.php'));

/***
 * Dummy renderer used for error messages when the requested renderer
 * doesn't exist
 */
class KintassaInvalidGalleryApplet extends KintassaGalleryApplet {
	static function register() {
		KintassaGalleryApplet::register('KintassaInvalidGalleryApplet', 'invalid', null);
	}

	function classes($suffix=null) {
		$cls = parent::classes($suffix);
		$cls[] = "kintassa-applet-invalid$suffix";
		return $cls;
	}

	function render() {
		$applet = $this;

		$gallery = $this->gallery;

		$unique_id = $this->unique_id();

		$cls = $this->classes_attrib_str();
		$outer_cls = $this->wrapper_classes_attrib_str();
		$sty = $this->styles_attrib_str();
		$companion_sty = $this->companion_styles_attrib_str();

		$not_avail_msg = __("This gallery cannot be displayed. Please check the gallery ID exists, (re)install the necessary GalleryApplets for its display method, or change the display method to one that's currently available.");

		$template = $this->template_path("invalid", "render");

		ob_start();
		require($template);
		$template_html = ob_get_contents();
		ob_end_clean();

		return $template_html;
	}
}

KintassaInvalidGalleryApplet::register();

?>
