<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_form.php");
require_once("kgal_gallery.php");

class KGalleryEditForm extends KGalleryForm {
	function __construct($name, $gallery_id) {
		$kgal = new KintassaGallery($gallery_id);

		$default_vals = array(
			"name"				=> $kgal->name,
			"width"				=> $kgal->width,
			"height"			=> $kgal->height,
			"display_mode"		=> $kgal->display_mode,
		);
		parent::__construct($name, $default_vals);

		$this->id_field = new KintassaHiddenField('id', $name='id', $default_val = $gallery_id);
		$this->add_child($this->id_field);

		$add_image_args = array("mode" => "galleryimage_add", "gallery_id" => $gallery_id);
		$add_image_link = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $add_image_args);
		$this->add_image_button = new KintassaLinkButton("Add Image", $name="add_image", $uri=$add_image_link);
		$this->add_child($this->add_image_button);
	}

	function render_success() {
		echo ("Gallery updated. Thank you.");
	}

	function update_record() {
		global $wpdb;

		$dat = $this->data();
		$fmt = $this->data_format();

		$where_dat = array("id"	=> $this->id_field->value());
		$where_fmt = array("%d");

		$wpdb->update(KintassaGallery::table_name(), $dat, $where_dat, $fmt, $where_fmt);

		return true;
	}
}

?>