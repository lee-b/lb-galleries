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
}

class KGalleryListForm extends KintassaWPForm {
	function KGalleryListForm() {
		parent::KintassaWPForm("gallery_list");
	}
	
	function generate_form() {
		$form_uri = $this->form_uri();
		$form_name = $this->form_name();
		
		$gal_table_form = new KGalleryTableForm('KintassaGallery');
		$gal_table_form->render($this->form_name());
		
		echo "<form method=\"post\" action=\"{$form_uri}\">";
		echo "    <input type=\"submit\" name=\"{$form_name}\" value=\"submit\">";
		echo "</form>";
	}
	
	function process_results() {
		echo "<p>Got it, thanks!</p>";
	}
}

?>
