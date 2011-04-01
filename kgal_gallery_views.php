<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_wp_form.php");
require_once("kgal_gallery.php");
require_once("kin_wp_tablepage.php");

class KGalleryTableRowOptions extends KintassaWPTableRowOptions {
	function KGalleryTableRowOptions($opt_flags) {
		parent::KintassaWPTableRowOptions($opt_flags);
	}

	function do_up($id) {
		echo "<p>Moving ${id} up.</p>";
	}

	function do_down($id) {
		echo "<p>Moving ${id} down.</p>";
	}

	function do_edit($id) {
		echo "<p>Editing ${id}.</p>";
	}

	function do_del($id) {
		echo "<p>Deleting ${id}.</p>";
	}
}

class KGalleryTablePage extends KintassaWPTablePage {
	function KGalleryTablePage($opt_flags = KintassaWPTableRowOptions::All) {
		$form_opts = new KGalleryTableRowOptions($opt_flags);
		parent::KintassaWPTablePage('KintassaGallery', $form_opts);
	}
}

?>
