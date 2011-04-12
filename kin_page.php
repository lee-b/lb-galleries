<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaPage {
	function __construct($title) {
		$this->title = $title;
	}

	function begin_page() {
		screen_icon();
		echo("<h2 class=\"title\">{$this->title}</h2>");
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