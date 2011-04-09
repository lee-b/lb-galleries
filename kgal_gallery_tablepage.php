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

class KGalleryTableForm extends KintassaOptionsTableForm {
	function do_row_action_add($row_id) {}
	function do_row_action_edit($row_id) {}
	function do_row_action_delete($row_id) {}
	function do_row_action_move_up($row_id) {}
	function do_row_action_move_down($row_id) {}

	function handle_submissions() {
		$recognised_actions = array("up", "down", "edit", "del");
		$actions_taken = $this->buttons_submitted($recognised_actions);

		if ($actions_taken) {
			$btn = $actions_taken[0];
			$form = $actions_taken[1];

			$row = $form->row;

			echo("<div class=\"error\">{$btn} row id: {$row->id}</div>");

			return true;
		}
	}
}

class KGalleryRowOptionsForm extends KintassaForm {
	const Sort = 1;
	const Edit = 2;
	const Delete = 4;
	const All = 7;

	function KGalleryRowOptionsForm($table_form, $row, $opts = KGalleryRowOptionsForm::All) {
		$form_name = $table_form->name() . "_row_" . $row->id;
		parent::KintassaForm($form_name);
		$this->row = $row;

		if ($opts & KGalleryRowOptionsForm::Sort) {
			$this->add_child(new KintassaButton("&uarr;", $name="up"));
			$this->add_child(new KintassaButton("&darr;", $name="down"));
		}

		if ($opts & KGalleryRowOptionsForm::Edit) {
			$this->add_child(new KintassaButton("Edit"));
		}

		if ($opts & KGalleryRowOptionsForm::Delete) {
			$this->add_child(new KintassaButton("Del"));
		}
	}

	function handle_submissions() {
		// subform, so we just ignore this
		return false;
	}
}

class KGalleryRowOptionsFactory extends KintassaRowFormFactory {
	function instanciate($table_form, $row) {
		return new KGalleryRowOptionsForm($table_form, $row);
	}
}

class KintassaDBResultsPager extends KintassaPager {
	function KintassaDBResultsPager($table_name, $page_size = 10) {
		$this->table_name =  $table_name;
		$this->page_size = $page_size;
		$this->results = null;
	}

	function build_query($page_num) {
		assert($page_num >= 1);

		// start at index 0
		$page_num = $page_num - 1;
		$start_item = $page_num * $this->page_size;
		if ($start_item < 1) {
			$start_item = 1;
		}

		$qry = "SELECT * FROM {$this->table_name} ORDER BY `name` ASC LIMIT {$start_item},{$this->page_size}";

		return $qry;
	}

	/***
	 * gets db results if not already cached, then returns them
	 */
	function get_db_results() {
		global $wpdb;

		if ($this->results == null) {
			$page_num = 1;
			$qry = $this->build_query($page_num);
			$this->results = $wpdb->get_results($qry);
		}

		return $this->results;
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
			$res[] = $row;
		}
		return $res;
	}
}

class KGalleryTablePage extends KintassaPage {
	function KGalleryTablePage($name) {
		parent::KintassaPage($name);

		$col_map = array(
			"Name",
			"Width",
			"Height",
			"Display Mode"
		);

		$table_name = KintassaGallery::table_name();
		$pager = new KintassaDBResultsPager($table_name);

		$row_form_fac = new KGalleryRowOptionsFactory($table_name);
		$this->table_form = new KGalleryTableForm($name, $col_map, $pager, $row_form_fac);
	}

	function content() {
		$this->table_form->execute();
	}
}

?>