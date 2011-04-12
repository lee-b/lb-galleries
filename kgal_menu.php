<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_tablepage.php");
require_once("kgal_galleryimage_tablepage.php");
require_once("kgal_gallery_addform.php");
require_once("kgal_gallery_editform.php");
require_once("kgal_galleryimage_addform.php");
require_once("kgal_galleryimage_editform.php");
require_once("kgal_image.php");
require_once("kgal_about_page.php");
require_once("kin_utils.php");

class KGalleryMenu {
	function __construct() {
		$this->menu_title = "Galleries";
		add_action('admin_menu', array(&$this, 'add_menus'));
	}

	function add_page($label, $perms, $method_name) {
		$page_title = $label;
		$menu_title = $label;
		$capability = $perms;
		$func = array(&$this, $method_name);
		add_menu_page($page_title, $menu_title, $capability, $this->classify_slug($method_name), &$func);
	}

	function classify_slug($slug) {
		$cls = get_class($this);
		$full_slug = $cls . "_" .  $slug;
		return $full_slug;
	}

	function add_subpage($parent, $label, $perms, $method_name) {
		$page_title = $label;
		$menu_title = $label;
		$capability = $perms;
		$func = array(&$this, $method_name);
		add_submenu_page($this->classify_slug($parent), $page_title, $menu_title, $capability, $this->classify_slug($method_name), &$func);
	}

	function add_menus() {
		$mainpage = 'mainpage';
		$this->add_page($this->menu_title, 'administrator', $mainpage);
		$this->add_subpage($mainpage, 'About', 'administrator', 'about');
	}

	function mainpage() {
		$recognised_modes = array(
			"gallery_list", "gallery_add", "gallery_edit",
			"galleryimage_add", "galleryimage_edit"
		);

		$mode = 'gallery_list';
		if (isset($_GET['mode'])) {
			$given_mode = $_GET['mode'];
			if (in_array($given_mode, $recognised_modes)) {
				$mode = $given_mode;
			} else {
				$mode = "unrecognised_mode";
			}
		}

		$mode_handler = 'handle_' . $mode;
		$this->$mode_handler();
	}

	function handle_unrecognised_mode() {
		echo("<div class=\"error\">Error: the requested mode is unrecognised, or not yet implemented.</div>");
	}

	function images_subform($gallery_id) {
		$form_name = "kgallery_images";

		$col_map = array(
			"id",
			"sort_pri",
			"Image" => "filepath",
			"Name",
			"Description",
			// gallery_id hidden
		);

		$table_name = KintassaGalleryImage::table_name();
		$pager = new KintassaGalleryImageDBResultsPager($table_name, $page_size = 10, $gallery_id=$gallery_id);

		$row_opts = KGalleryImageRowOptionsForm::All;
		$row_form_fac = new KGalleryImageRowOptionsFactory($row_opts);
		$images_table_form = new KGalleryImageTableForm($form_name, $col_map, $pager, $row_form_fac);
		$images_table_form->execute();
	}

	function handle_gallery_list() {
		require_once("kgal_gallery.php");

		$pg = new KGalleryTablePage("kgallery_table", $this->menu_title);
		$pg->execute();
	}

	function handle_gallery_edit() {
		screen_icon();
		echo '<h2>' . __("Edit Gallery") . '</h2>';

		$gallery_id = $_GET['id'];
		assert (KintassaUtils::isInteger($gallery_id));

		$editForm = new KGalleryEditForm("kgal_edit", $gallery_id);
		$editForm->execute();

		$this->images_subform($gallery_id);
	}

	function handle_gallery_add() {
		screen_icon();
		echo '<h2>' . __("Add Gallery") . '</h2>';
		$addForm = new KGalleryAddForm("kgallery_add");
		$addForm->execute();
	}

	function handle_galleryimage_edit() {
		screen_icon();
		echo('<h2>' . __("Edit Gallery Image") . '</h2>');

		$gallery_image_id = $_GET['id'];
		assert (KintassaUtils::isInteger($gallery_image_id));

		$editForm = new KGalleryImageEditForm("kgalimage_edit", $gallery_image_id);
		$editForm->execute();
	}

	function handle_galleryimage_add() {
		screen_icon();
		echo '<h2>' . __("Add Gallery Image") . '</h2>';

		$addForm = new KGalleryImageAddForm("kgalimage_add");
		$addForm->execute();
	}

	function about() {
		$about_page = new KGalleryAboutPage(__("About Kintassa Galleries"));
		$about_page->execute();
	}
}

?>