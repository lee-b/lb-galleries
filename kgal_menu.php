<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaForm {
	function KintassaForm($name) {
		$this->name = $name;
		$this->serial = "";
	}
	
	function form_name() {
		return "kin_frm_{$this->name}_{$this->serial}";
	}
	
	function have_answers() {
		return isset($_POST[$this->form_name()]);
	}

	function form_uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	abstract function generate_form();
	abstract function process_results();
	
	function execute() {
		if ($this->have_answers()) {
			$this->process_results();
		} else {
			$this->generate_form();
		}
	}
}

class KGalleryListForm extends KintassaForm {
	function KGalleryListForm() {
		parent::KintassaForm("gallery_list");
	}
	
	function generate_form() {
		$form_uri = $this->form_uri();
		$form_name = $this->form_name();
		
		echo '<p>This is the options page</p>';

		echo "<form method=\"post\" action=\"{$form_uri}\">";
		echo "    <input type=\"submit\" name=\"{$form_name}\" value=\"submit\">";
		echo "</form>";
	}
	
	function process_results() {
		echo "<p>Got it, thanks!</p>";
	}
}

class KGalleryMenu {
	function KGalleryMenu() {
		$this->menu_title = "Kintassa Gallery";
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
		echo '<h2>' . $this->menu_title . '</h2>';
		
		$frm = new KGalleryListForm();
		$frm->execute();
	}
}

?>
