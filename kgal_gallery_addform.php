<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_form.php");

class KGalleryAddForm extends KGalleryForm {
	function KGalleryAddForm($name) {
		$default_vals = array(
			"name"			=> "",
			"width"			=> 320,
			"height"		=> 200,
			"display_mode"	=> "slideshow",
		);
		parent::__construct($name, $default_vals);
	}

	function render_success_page() {
		$page_args = array("mode" => "gallery_edit", "id" => $this->id);
		$edit_uri = KintassaUtils::admin_path('KGalleryMenu', 'mainpage', $page_args);

		echo("<h2>" . __("Gallery Added") . "</h2>");
		echo(
			"<p>"
			. __("Your gallery has been added.  You might want to <a href=\"{$edit_uri}\">Edit this gallery</a> now.")
			. "</p>"
		);
	}

	function update_record() {
		// create and populate the db record in one step
		global $wpdb;

		$dat = $this->data();
		$fmt = $this->data_format();

		$wpdb->insert(KintassaGallery::table_name(), $dat, $fmt);
		$this->id = $wpdb->insert_id;

		// TODO: could add error reporting here

		$this->render_success_page();
		return true;
	}
}

?>