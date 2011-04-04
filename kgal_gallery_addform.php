<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_wp_form.php");
require_once("kgal_gallery.php");

class KGalleryAddForm extends KintassaWPForm {
	function KGalleryAddForm() {
		parent::KintassaWPForm('KGalleryAddForm');

		$this->add_child(new KintassaTextField("Name"));
		$this->add_child(new KintassaIntegerField("Width"), $default=320);
		$this->add_child(new KintassaIntegerField("Height"), $default=200);

		$displayMethodField = new KintassaRadioGroup("Display method");
		$displayMethodField->add_child(new KintassaRadioButton("Slideshow"));
		$displayMethodField->add_child(new KintassaRadioButton("Manual Slideshow"));
		$this->add_child($displayMethodField);

		$this->add_child(new KintassaCheckbox("Show navbar"));
		$this->add_child(new KintassaButton("Confirm"));
	}

	function render() {
		if ($this->have_submission('Add')) {
			echo "(Add results here)";
		} else {
			parent::render();
		}
	}
}

?>
