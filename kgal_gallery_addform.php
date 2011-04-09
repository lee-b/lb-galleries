<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kgal_gallery.php");

abstract class KGalleryForm extends KintassaForm {
	function KGalleryForm($name) {
		parent::KintassaForm($name);

		$this->name_field = new KintassaTextField("Name");
		$this->add_child($this->name_field);

		$dimensions_band = new KintassaFieldBand("dimensions_band");
		$this->width_field = new KintassaIntegerField("Width");
		$dimensions_band->add_child($this->width_field, $default=320);
		$this->height_field = new KintassaIntegerField("Height");
		$dimensions_band->add_child($this->height_field, $default=200);
		$this->add_child($dimensions_band);

		$this->display_mode_field = new KintassaRadioGroup("Display method");
		foreach ($this->slideshow_options() as $sshow_opt) {
			$this->display_mode_field->add_child(new KintassaRadioButton($sshow_opt));
		}
		$this->add_child($this->display_mode_field);

		$this->add_child(new KintassaCheckbox("Show navbar"));

		$button_bar = new KintassaFieldBand("button_bar");
		$button_bar->add_child(new KintassaButton("Confirm"));
		$this->add_child($button_bar);
	}

	function slideshow_options() {
		return array(
			"Slideshow",
			"Manual slideshow",
		);
	}

	function data() {
		$dat = array(
			"name"			=> $this->name_field->value(),
			"width"			=> $this->width_field->value(),
			"height"		=> $this->height_field->value(),
			"display_mode"	=> $this->display_mode_field->value(),
		);
		return $dat;
	}

	function data_format() {
		$fmt = array(
			"%s",
			"%d",
			"%d",
			"%s"
		);
		return $fmt;
	}

	/// update the record in the database, based on the form details
	abstract function update_record();

	function handle_submissions() {
		$action_form = $this->buttons_submitted(array('confirm'));
		if ($action_form != null) {
			$this->update_record();
			return true;
		}

		return false;
	}
}

class KGalleryAddForm extends KGalleryForm {
	function update_record() {
		// create and populate the db record in one step
		global $wpdb;

		$dat = $this->data();
		$fmt = $this->data_format();

		$wpdb->insert(KintassaGallery::table_name(), $dat, $fmt);
		$this->id = $wpdb->insert_id;
	}
}

class KGalleryEditForm extends KGalleryForm {
	function KGalleryEditForm($name, $gallery_id) {
		parent::KGalleryForm($name);

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
	}
}

?>