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

	function button_name($btn) {
		return $this->form_name() . "_" . $btn . "_btn";
	}

	function render_button($name, $label) {
		$btn_name = $this->button_name($name);
		echo("<input type=\"submit\" value=\"{$label}\" name=\"{$btn_name}\">");
	}
	
	function have_submission($btn) {
		$real_btn_name = $this->button_name($btn);
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