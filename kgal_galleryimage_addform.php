<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_galleryimage_form.php");

class KGalleryImageAddForm extends KGalleryImageForm {
	function KGalleryAddForm($name) {
		$default_vals = array(
			"name"			=> "",
			"width"			=> 320,
			"height"		=> 200,
			"display_mode"	=> "slideshow",
		);
		parent::__construct($name, $default_vals);
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