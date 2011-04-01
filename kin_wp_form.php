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
	
	function form_name() {
		return "kin_frm_{$this->name}_{$this->serial}";
	}
	
	function have_answers() {
		return isset($_POST[$this->form_name()]);
	}

	function form_uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	abstract function generate_form();
	abstract function process_results();
	
	function execute() {
		if ($this->have_answers()) {
			$this->process_results();
		} else {
			$this->generate_form();
		}
	}
}

?>