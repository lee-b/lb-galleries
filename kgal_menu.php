<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery_tablepage.php");
require_once("kgal_gallery_addform.php");

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

		echo '<h2>' . $this->menu_title . '</h2>';

		$pg = new KGalleryTablePage("GalleryTable", 'KintassaGallery');
		$pg->execute();
	}

	function add_gallery() {
		echo '<h2>' . $this->menu_title . '</h2>';

		$addForm = new KGalleryAddForm();
		$addForm->execute();
	}

	function about() {
		echo '<h2>' . $this->menu_title . '</h2>';

		echo "Copyright &copy; 2011 by Kintassa.  All rights reserved.";

		// TODO: not implemented
	}
}

?>