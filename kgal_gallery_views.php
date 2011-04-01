<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_wp_form.php");
require_once("kgal_gallery.php");
require_once("kin_wp_tableform.php");

class KGalleryTableForm extends KintassaWPTableForm {
	function render_add_new_form() {
		// TODO: not implemented
		echo "(Adding new entry)";
	}

	function render_add_new_results() {
		// TODO: not implemented
		echo "(Entry added)";
	}
}

?>
