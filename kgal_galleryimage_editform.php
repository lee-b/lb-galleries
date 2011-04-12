<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_galleryimage_form.php");
require_once("kgal_gallery.php");

class KGalleryImageEditForm extends KGalleryImageForm {
	function __construct($name, $gallery_image_id) {
		$img = new KintassaGalleryImage($gallery_image_id);

		$default_vals = array(
			"name"				=> $img->name,
			"sort_pri"			=> $img->sort_pri,
			"filepath"			=> $img->filepath,
			"description"		=> $img->description,
			"mimetype"			=> $img->mimetype,
			"gallery_id"		=> $img->gallery_id,
		);
		parent::__construct($name, $default_vals);

		$this->id_field = new KintassaHiddenField('id', $name='id', $default_val = $gallery_image_id);
		$this->add_child($this->id_field);
	}

	function render_success() {
		echo ("Image updated. Thank you.");
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