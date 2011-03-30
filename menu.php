<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

namespace kintassa\wp_gallery;

class Menu {
	function Menu($menu_name) {
		$this->menu_title = $menu_name;
		add_action('admin_menu', array(&$this, 'add_menus'));
	}

	function add_page($label, $perms, $method_name) {
		$page_title = $label;
		$menu_title = $label;
		$capability = $perms;
		$slug = get_class($this) . '_' . $method_name;
		$func = array(&$this, $method_name);
		add_menu_page($page_title, $menu_title, $capability, $slug, &$func);
	}
	
	function add_menus() {
		$this->add_page($this->menu_title, 'administrator', 'mainpage');
	}
	
	function mainpage() {
		echo '<h1>' . $this->menu_title . '</h1>';
		
		echo '<p>This is the galassa options page';
	}
}

?>
