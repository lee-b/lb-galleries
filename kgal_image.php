<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kin_micro_orm.php');

class KintassaGalleryImage extends KintassaMicroORMObject {
	function KintassaGalleryImage($id = null) {
		parent::KintassaMicroORMObject(KintassaGalleryImage::table_name, $id);

		if (!$this->is_loaded()) {
			// TODO: Not implemented
		}
	}

	static function table_name() {
		global $wpdb;
		return $wpdb->prefix . "kintassa_gal_img";
	}

	function save() {
		// TODO: Not implemented
	}

	function load($id) {
		global $wpdb;

		assert ($this->id != null);

		// TODO: Not implemented
	}

	function render($width = null, $height = null) {
		if (!$this->is_loaded()) {
			$this->load();

			$gallery_code = "<div class=\"kintassa_gallery\"";

			$style_code = "";
			if ($width) { $style_code .= "width: {$width};"; }
			if ($height) { $style_code .= "height: {$height};"; }
			if (strlen($style_code) > 0) { $gallery_code .= " style=\"{$style_code}\""; }

			$gallery_code .= ">GALLERY NUMBER {$this->id}</div>";

			return $gallery_code;
		}
	}
}

?>