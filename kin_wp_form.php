<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaWPForm {
	function KintassaWPForm($name) {
		$this->name = $name;
		$this->serial = "";
	}

	function horiz_spacer() {
		echo("&nbsp;");
	}

	function begin_form() {
		$form_uri = $this->form_uri();
		$form_name = $this->form_name();
		echo "<form method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\">";
	}

	function end_form() {
		echo "</form>";
	}

	function form_name() {
		return "kin_frm_{$this->name}_{$this->serial}";
	}

	function field_name($field) {
		return $this->form_name() . "_" . $field;
	}

	function choose_name($label, $provided_name) {
		if ($provided_name != null) {
			$name = $provided_name;
		} else {
			$name = strtolower($label);
			$name = str_replace(" ", "_", $name);
		}
		return $name;
	}

	function add_button($label, $name=null) {
		$btn_name = $this->field_name($this->choose_name($label, $name));
		echo("<div>");
		echo("<input type=\"submit\" value=\"{$label}\" name=\"{$btn_name}\">");
		echo("</div>");
	}

	function add_text_field($label, $name=null) {
		$name = $this->field_name($this->choose_name($label, $name));
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function add_number_field($label, $name=null) {
		$name = $this->field_name($this->choose_name($label, $name));
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function add_checkbox_field($label, $name=null) {
		$name = $this->field_name($this->choose_name($label, $name));
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function begin_radio_field($label) {
		$f = null;

		// TODO: not implemented

		return $f;
	}

	function add_radio_option($rf, $label, $name=null) {
		$name = $this->field_name($this->choose_name($label, $name));
		// TODO: not implemented
	}

	function end_radio_field($rf) {
		// TODO: not implemented
	}

	function have_submission($btn) {
		$real_btn_name = $this->field_name($btn);
		$set = isset($_POST[$real_btn_name]);
		return $set;
	}

	function form_uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	abstract function execute();
}

/***
 * Represents a very simple, complete-and-submit style single-option form,
 * where the only submit button is simply called 'submit'.
 */
abstract class KintassaWPSimpleForm {
	abstract function generate_form();
	abstract function process_results();

	abstract function render_form_contents();

	function render_form() {
		$this->begin_form();
		$this->render_form_contents();
		$this->end_form();
	}

	function end_form() {
		$this->render_button("Submit", _e("Submit"));
		parent::end_form();
	}

	function execute() {
		if ($this->have_submission('Submit')) {
			$this->process_results();
		} else {
			$this->render_form();
		}
	}
}

?>