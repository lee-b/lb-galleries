<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_config.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "kintassa_core" . DIRECTORY_SEPARATOR . "kin_page.php");
require_once("kgal_gallery_tableform.php");

class KGalleryTablePage extends KintassaPage {
	function __construct($title) {
		parent::__construct($title);

		$table_name = "kgallery_list";

		$col_map = array(
			"id"				=> null,
			"name"				=> "Name",
			"width"				=> "Width",
			"height"			=> "Height",
			"display_mode"		=> "Display Mode"
		);

		$table_name = KintassaGallery::table_name();
		$pager = new KintassaGalleryDBResultsPager($table_name);

		$row_opts = KGalleryRowOptionsForm::Edit | KGalleryRowOptionsForm::Delete;
		$row_form_fac = new KGalleryRowOptionsFactory($row_opts);
		$this->table_form = new KGalleryTableForm($table_name, $col_map, $pager, $row_form_fac);
	}

	function content() {
		$this->table_form->execute();

		$page_args = array(
			"mode" => "gallery_add",
		);
		$page_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $page_args);

		echo("<a href=\"{$page_uri}\" class=\"button\">Add Gallery</a>");
	}
}

?>