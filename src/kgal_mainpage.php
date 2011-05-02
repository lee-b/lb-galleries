<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_page.php'));
require_once('kgal_config.php');
require_once('kgal_gallery_editpage.php');
require_once('kgal_gallery_addpage.php');
require_once('kgal_gallery_tablepage.php');
require_once('kgal_galleryimage_addpage.php');
require_once('kgal_galleryimage_editpage.php');

class KGalleryMainPage extends KintassaPage {
	function content() {
		$recognised_modes = array(
			"gallery_list"			=> array("KGalleryTablePage", __("Kintassa Galleries")),
			"gallery_add"			=> array("KGalleryAddPage", __("Add Gallery")),
			"gallery_edit"			=> array("KGalleryEditPage", __("Edit Gallery")),
			"galleryimage_add"		=> array("KGalleryImageAddPage", __("Add Image")),
			"galleryimage_edit"		=> array("KGalleryImageEditPage", __("Edit Image"))
		);

		// determine appropriate mode from web request
		$mode = 'gallery_list';	// default mode
		if (isset($_GET['mode'])) {
			$given_mode = $_GET['mode'];

			if (array_key_exists($given_mode, $recognised_modes)) {
				$mode = $given_mode;
			}
		}

		// determine the correct function handler for the mode, and call it
		$handler_details = $recognised_modes[$mode];
		$page_handler_class = $handler_details[0];
		$page_title = $handler_details[1];

		$page_handler = new $page_handler_class($page_title);
		$page_handler->execute();
	}
}

?>