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
		echo("<h2>Gallery Added</h2>");
		echo(<<<HTML
<p>Your gallery has been added.  You might want to
<a href="/wp-admin/admin.php?page=KGalleryMenu_mainpage&mode=gallery_edit&id={$this->id}">Edit this
gallery</a> now.
HTML
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