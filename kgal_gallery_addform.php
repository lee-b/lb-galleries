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
	}

	function execute() {
		if ($this->have_submission('Add')) {
			echo "(Add results here)";
		} else {
			$this->begin_form();

			$this->add_text_field("Name");

			$this->add_number_field("Width");
			$this->add_number_field("Height");

			$radio = $this->begin_radio_field("Display method");
			$this->add_radio_option($radio, "Slideshow");
			$this->add_radio_option($radio, "Manual Slideshow");
			$this->end_radio_field($radio);

			$this->add_checkbox_field("Show navbar");

			$this->add_button("Confirm");

			$this->end_form();
		}
	}
}

?>
