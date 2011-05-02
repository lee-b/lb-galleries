<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once('kgal_galleryimage_addform.php');

class KGalleryImageAddPage extends KintassaPage {
	function __construct($title) {
		parent::__construct($title);

		if (!isset($_GET['gallery_id']) || !KintassaUtils::isInteger($_GET['gallery_id'])) {
			echo("<div class=\"error\">Error: invalid gallery id specified</div>");
			return;
		} else {
			$gallery_id = $_GET['gallery_id'];
		}

		$default_vals = array(
			"sort_pri"		=> 0,
			"filepath"		=> null,
			"name"			=> null,
			"description"	=> "",
			"gallery_id"	=> $gallery_id,
		);
		$this->addForm = new KGalleryImageAddForm("kgalimage_add", $default_vals);
	}

	function content() {
		$this->addForm->execute();
	}
}

?>
