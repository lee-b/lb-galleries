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

		$dimensions_band = new KintassaFieldBand("dimensionsband");
		$dimensions_band->add_child(new KintassaIntegerField("Width"), $default=320);
		$dimensions_band->add_child(new KintassaIntegerField("Height"), $default=200);
		$this->add_child($dimensions_band);

		$displayMethodField = new KintassaRadioGroup("Display method");
		$displayMethodField->add_child(new KintassaRadioButton("Slideshow"));
		$displayMethodField->add_child(new KintassaRadioButton("Manual Slideshow"));
		$this->add_child($displayMethodField);

		$this->add_child(new KintassaCheckbox("Show navbar"));

		$button_bar = new KintassaFieldBand("button_bar");
		$button_bar->add_child(new KintassaButton("Confirm"));
		$this->add_child($button_bar);
	}

	function handle_submissions() {
		if ($this->have_submission('confirm')) {
			echo("Form submitted");
			return true;
		}

		return false;
	}
}

?>