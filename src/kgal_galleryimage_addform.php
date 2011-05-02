<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_galleryimage_form.php");

class KGalleryImageAddForm extends KGalleryImageForm {
	function __construct($name, $default_vals) {
		$internal_defaults = array();

		$internal_defaults["name"] = $name;
		$internal_defaults["width"] = 320;
		$internal_defaults["height"] = 200;
		$internal_defaults["display_mode"] = "slideshow";

		$new_defaults = array_merge($internal_defaults, $default_vals);

		parent::__construct($name, $new_defaults);
	}

	function render_success() {
		$page_args = array("mode" => "galleryimage_edit", "id" => $this->id);
		$edit_uri = KintassaUtils::admin_path('KGalleryMenu', 'mainpage', $page_args);

		echo("<h2>" . __("Image Added") . "</h2>");
		echo("<p>" . __("Your gallery image has been added.  Thank you.") . "</p>");

		$this->gallery_return_link();
	}

	function update_record() {
		// create and populate the db record in one step
		global $wpdb;

		$dat = $this->data();
		$fmt = $this->data_format();

		$wpdb->insert(KintassaGalleryImage::table_name(), $dat, $fmt);
		$this->id = $wpdb->insert_id;

		return true;
	}
}

?>
