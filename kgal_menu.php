<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_tablepage.php");
//require_once("kgal_galimage_tablepage.php");
require_once("kgal_gallery_addform.php");
require_once("kgal_gallery_editform.php");
require_once("kgal_about_page.php");
require_once("kin_utils.php");

class KGalleryMenu {
	function __construct() {
		$this->menu_title = "Kintassa Galleries";
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
		$this->add_subpage($mainpage, 'Add Gallery', 'administrator', 'add_gallery');
		$this->add_subpage($mainpage, 'About', 'administrator', 'about');
	}

	function mainpage() {
		$recognised_modes = array("gallery_list", "gallery_edit");

		$mode = 'gallery_list';
		if (isset($_GET['mode'])) {
			$given_mode = $_GET['mode'];
			if (in_array($given_mode, $recognised_modes)) {
				$mode = $given_mode;
			}
		}

		$mode_handler = 'handle_' . $mode;
		$this->$mode_handler();
	}

	function handle_gallery_list() {
		require_once("kgal_gallery.php");

		$pg = new KGalleryTablePage("kgallery_table", $this->menu_title);
		$pg->execute();
	}

	function handle_gallery_edit() {
		$gallery_id = $_GET['id'];

		assert (KintassaUtils::isInteger($gallery_id));
		// TODO: check id exists

		$editForm = new KGalleryEditForm("kgal_edit", $gallery_id);
		$editForm->execute();
	}

	function add_gallery() {
		// TODO: convert to KGalleryAddPage()
		screen_icon();
		echo '<h2>' . $this->menu_title . '</h2>';
		$addForm = new KGalleryAddForm("kgallery_add");
		$addForm->execute();
	}

	function about() {
		$about_page = new KGalleryAboutPage("About {$this->menu_title}");
		$about_page->execute();
	}
}

?>