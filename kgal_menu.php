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
require_once("kgal_about_page.php");

class KGalleryMenu {
	function KGalleryMenu() {
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
		require_once("kgal_gallery.php");

		$pg = new KGalleryTablePage("kgallery_table", $this->menu_title);
		$pg->execute();
	}

	function add_gallery() {
		// TODO: convert to KGalleryAddPage()
		echo '<h2>' . $this->menu_title . '</h2>';
		$addForm = new KGalleryAddForm();
		$addForm->execute();
	}

	function about() {
		$about_page = new KGalAboutPage();
		$about_page->execute();
	}
}

?>
