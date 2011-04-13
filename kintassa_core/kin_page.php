<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

abstract class KintassaPage {
	function __construct($title) {
		$this->title = $title;
	}

	function begin_page() {
		echo("<div id=\"icon-tools\" class=\"icon32\"><br></div>");
		echo("<h2 class=\"title\">" . __($this->title) . "</h2>");
	}

	function end_page() {
	}

	abstract function content();

	function execute() {
		$this->begin_page();
		$this->content();
		$this->end_page();
	}
}

?>