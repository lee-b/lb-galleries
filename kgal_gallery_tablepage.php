<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kgal_gallery.php");
require_once("kin_tableform.php");

class KGalleryTableForm extends KintassaRowOptionsTableForm {
	function do_row_action_add($row_id) {}
	function do_row_action_edit($row_id) {}
	function do_row_action_delete($row_id) {}
	function do_row_action_move_up($row_id) {}
	function do_row_action_move_down($row_id) {}
}

class KGalleryTableRowOptionsFactory extends KintassaRowFormFactory {
	function instanciate($table_form, $row_id) {
		// TODO: implement this
	}
}

class KintassaDBResultsPager extends KintassaPager {
	function KintassaDBResultsPager($current_page) {
		$current_page = $current_page;
	}

	/***
	 * gets db results if not already cached, then returns them
	 */
	function get_db_results() {
		return array(); // TODO: implement this
	}

	function current_page() {
		return $this->current_page;
	}

	function num_pages() {
		return $this->results->count();
	}

	function items_on_page() {
		$db_results = $this->get_db_results();

		$res = array();
		foreach ($db_results as $row) {
			$res[] = $row['id'];
		}
		return $res;
	}
}

class KGalleryTablePage extends KintassaPage {
	function KGalleryTablePage($name) {
		parent::KintassaPage('KintassaGallery');

		$form_name = $name;
		$col_map = array(
			"Name",
			"Width",
			"Height",
			"Display Mode"
		);
		$pg = 1;
		$pager = new KintassaDBResultsPager($pg);
		$row_form_fac = new KGalleryTableRowOptionsFactory();
		$this->table_form = new KGalleryTableForm($name, $col_map, $pager, $row_form_fac);
	}

	function execute() {
		$this->table_form->render();
	}
}

?>
