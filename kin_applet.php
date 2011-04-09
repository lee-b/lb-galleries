<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

/***
 * An HTML applet which is displayed on a webpage
 */
abstract class KintassaApplet {
	function __construct() {
	}

	function add_stylesheet($rel_path) {
		// TODO: Implement add_stylesheet
		exit("Not implemented");
	}

	function add_script($rel_path) {
		// TODO: Implement add_script
		exit("Not implemented");
	}

	abstract function render();
}

?>