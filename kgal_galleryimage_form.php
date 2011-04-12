<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kgal_gallery.php");

abstract class KGalleryImageForm extends KintassaForm {
	function __construct($name, $default_vals) {
		parent::__construct($name);
		$this->add_widgets($default_vals);
	}

	function add_widgets($def) {
		$this->sort_pri_field = new KintassaIntegerField(
			"Sort Priority", $name="sort_pri",
			$default_value=$def['sort_pri'], $required=true
		);
		$this->add_child($this->sort_pri_field);

		$this->name_field = new KintassaTextField(
			"Name", $name="name",
			$default_value = $def['name'], $required=true
		);
		$this->add_child($this->name_field);

		$this->image_field = new KintassaImageUploadField(
			"Image", $name="filepath", $default_value = "", $required=true);
		$this->add_child($this->image_field);

		$this->gallery_id = new KintassaHiddenField(
			"Gallery ID", $name="gallery_id",
			$default_value=$def['gallery_id'], $required=true
		);

		$button_bar = new KintassaFieldBand("button_bar");
		$confirm_button = new KintassaButton(
			"Confirm", $name="confirm", $primary = true
		);
		$button_bar->add_child($confirm_button);
		$this->add_child($button_bar);
	}

	function data() {
		$dat = array(
			"name"					=> $this->name_field->value(),
			"width"					=> $this->width_field->value(),
			"height"				=> $this->height_field->value(),
			"display_mode"			=> $this->display_mode_field->value(),
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

	function is_valid() {
		if (!parent::is_valid()) return false;
		return $this->buttons_submitted(array('confirm')) != null;
	}

	function handle_submissions() {
		$res = $this->update_record();
		if ($res) {
			$this->render_success();
		}

		return $res;
	}
}

?>