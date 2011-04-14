<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_config.php");
require_once("kgal_mainpage.php");
require_once("kgal_gallery_addform.php");
require_once("kgal_gallery_editform.php");
require_once("kgal_gallery.php");
require_once("kgal_image.php");
require_once("kgal_about_page.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "kintassa_core" . DIRECTORY_SEPARATOR . "kin_utils.php");

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
		$title = null; // mainpage is just a dispatcher, so no title
		$main_page = new KGalleryMainPage($title);
		$main_page->execute();
	}

	function about() {
		$about_page = new KGalleryAboutPage(__("About Kintassa Galleries"));
		$about_page->execute();
	}
}

?>