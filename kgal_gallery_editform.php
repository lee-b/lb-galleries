<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_form.php");


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

		$this->id_field = new KintassaHiddenField('id', $default_value = $gallery_id);
		$this->add_child($this->id_field);
	}

	function update_record() {
		global $wpdb;

		$dat = $this->data();
		$fmt = $this->data_format();

		$where = array(
			"id"			=> $this->id_field->value(),
		);

		$where_fmt = array(
			"%d"
		);

		$wpdb->update(KGalleryTable::table_name(), $dat, $where, $fmt, $where_fmt);

		// TODO: could add error reporting here
		return true;
	}
}

?>