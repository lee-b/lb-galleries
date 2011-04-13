<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_config.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "kintassa_core/kin_form.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "kintassa_core/kin_utils.php");
require_once("kgal_gallery.php");

abstract class KGalleryForm extends KintassaForm {
	function __construct($name, $default_vals) {
		parent::__construct($name);
		$this->add_widgets($default_vals);
	}

	function add_widgets($def) {
		$this->name_field = new KintassaTextField("Name", $name="name", $default_value = $def['name'], $required=true);
		$this->add_child($this->name_field);

		$dimensions_band = new KintassaFieldBand("dimensions_band");
		$this->width_field = new KintassaIntegerField("Width", $name="width", $default_value = $def['width'], $required=true);
		$dimensions_band->add_child($this->width_field);
		$this->height_field = new KintassaIntegerField("Height", $name="height", $default_value = $def['height'], $required=true);
		$dimensions_band->add_child($this->height_field);
		$this->add_child($dimensions_band);

		$this->display_mode_field = new KintassaRadioGroup("Display method", $name="display_mode", $default_value=$def['display_mode']);

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
			"name"					=> $this->name_field->value(),
			"width"					=> $this->width_field->value(),
			"height"				=> $this->height_field->value(),
			"display_mode"			=> $this->display_mode_field->value(),
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