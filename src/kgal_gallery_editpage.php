<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_utils.php'));
require_once('kgal_config.php');
require_once('kgal_image.php');
require_once('kgal_galleryimage_editform.php');
require_once('kgal_galleryimage_tablepage.php');

class KGalleryEditPage extends KintassaPage {
	function __construct($title) {
		parent::__construct($title);

		$gallery_id = $_GET['id'];
		assert (KintassaUtils::isInteger($gallery_id));

		$this->gallery_id = $gallery_id;

		$this->editForm = new KGalleryEditForm("kgal_edit", $gallery_id);
	}

	function images_subform() {
		$form_name = "kgallery_images";

		$col_map = array(
			"id"			=> null,
			"sort_pri"		=> "Sort Order",
			"filepath"		=> "Image",
			"name"			=> "Name",
			"description"	=> "Description"
		);

		$table_name = KintassaGalleryImage::table_name();
		$pager = new KintassaGalleryImageDBResultsPager(
			$table_name, $page_size = 10, $gallery_id=$this->gallery_id
		);

		$row_opts = KGalleryImageRowOptionsForm::All;
		$row_form_fac = new KGalleryImageRowOptionsFactory($row_opts);
		$images_table_form = new KGalleryImageTableForm($form_name, $col_map, $pager, $row_form_fac);
		$images_table_form->execute();
	}

	function add_options() {
		$add_image_args = array("mode" => "galleryimage_add", "gallery_id" => $this->gallery_id);
		$add_image_link = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $add_image_args);

		$this->add_image_button = new KintassaLinkButton("Add Image", $name="add_image", $uri=$add_image_link);
		$this->add_image_button->render();
	}

	function content() {
		$this->editForm->execute();
		$this->images_subform();
		$this->add_options($this->editForm);
	}
}

?>