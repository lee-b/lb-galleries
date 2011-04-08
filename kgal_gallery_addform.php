<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kgal_gallery.php");

class KGalleryAddForm extends KintassaForm {
	function KGalleryAddForm() {
		parent::KintassaForm('kgalleryaddform');

		$this->add_child(new KintassaTextField("Name"));
		$this->add_child(new KintassaIntegerField("Width"), $default=320);
		$this->add_child(new KintassaIntegerField("Height"), $default=200);

		$displayMethodField = new KintassaRadioGroup("Display method");
		$displayMethodField->add_child(new KintassaRadioButton("Slideshow"));
		$displayMethodField->add_child(new KintassaRadioButton("Manual Slideshow"));
		$this->add_child($displayMethodField);

		$this->add_child(new KintassaCheckbox("Show navbar"));

		$this->confirm = new KintassaButton("Confirm");
		$this->add_child($this->confirm);
	}

	function render() {
		$post_vars =& $_POST;
		$file_vars =& $_FILE;

		if ($this->is_valid($post_vars, $file_vars)) {
			if ($this->have_submission('confirm')) {
//			if ($this->confirm->submitted($post_vars, $file_vars)) {
				echo("(Add results here)");
			} else {
				echo ("(No button detected)");
				parent::render();
			}
		} else {
			echo("(Form not valid)");
			parent::render();
		}
	}
}

?>