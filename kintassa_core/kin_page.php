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
		// only render a wrapper if this page has a title of its own;
		// some pages are only dispatchers for other pages
		if ($this->title) {
			echo("<div class=\"kintassa-page\">");
			echo("<h2 class=\"title\">" . $this->title . "</h2>");
		}
	}

	function end_page() {
		if ($this->title) {
			echo("</div>");
		}
	}

	abstract function content();

	function execute() {
		$this->begin_page();
		$this->content();
		$this->end_page();
	}
}

?>