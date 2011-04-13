<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_galleryimage_editform.php");

class KGalleryImageEditPage extends KintassaPage {
	function __construct($title) {
		parent::__construct($title);

		$gallery_image_id = $_GET['id'];
		assert (KintassaUtils::isInteger($gallery_image_id));

		$this->editForm = new KGalleryImageEditForm("kgalimage_edit", $gallery_image_id);
	}

	function content() {
		$this->editForm->execute();
	}
}

?>
