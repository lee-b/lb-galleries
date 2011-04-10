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
	function __construct($name) {
		parent::__construct($name);

		$this->name_field = new KintassaTextField("Name");
		$this->add_child($this->name_field);

		$dimensions_band = new KintassaFieldBand("dimensions_band");
		$this->width_field = new KintassaIntegerField("Width");
		$dimensions_band->add_child($this->width_field, $default=320);
		$this->height_field = new KintassaIntegerField("Height");
		$dimensions_band->add_child($this->height_field, $default=200);
		$this->add_child($dimensions_band);

		$this->display_mode_field = new KintassaRadioGroup("Display method");

		$sshow_opts = $this->slideshow_options();
		foreach (array_keys($sshow_opts) as $sshow_opt_name) {
			$sshow_opt_label = $sshow_opts[$sshow_opt_name];
			$opt_el = new KintassaRadioButton($sshow_opt_label, $name=$sshow_opt_name);
			$this->display_mode_field->add_child($opt_el);
		}
		$this->add_child($this->display_mode_field);

		$button_bar = new KintassaFieldBand("button_bar");
		$confirm_button = new KintassaButton("Confirm", $name="confirm", $primary = true);
		$button_bar->add_child($confirm_button);
		$this->add_child($button_bar);
	}

	function slideshow_options() {
		return array(
			"slideshow"				=> "Slideshow",
			"manual_slideshow"		=> "Manual Slideshow"
		);
	}

	function data() {
		$dat = array(
			"name"			=> $this->name_field->value(),
			"width"			=> $this->width_field->value(),
			"height"		=> $this->height_field->value(),
			"display_mode"	=> $this->display_mode_field->value(),
		);

		// TODO: temporary validation check.  Should be removed, or modified
		//       to use options registered as KintassaGalleryApp classes.
		assert (in_array($dat['display_mode'], array('slideshow', 'manual_slideshow')));

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
		$valid = parent::is_valid();
		if (!$valid) return $valid;
		return $this->buttons_submitted(array('confirm')) != null;
	}

	function handle_submissions() {
		$res = $this->update_record();
		if ($res) {
			echo ("Gallery updated.  Thank you.");
		}

		return $res;
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

		// TODO: could add error reporting here
		return true;
	}
}

?>