<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kin_page.php");

abstract class KintassaPager {
	abstract function current_page();
	abstract function num_pages();
	abstract function items_on_page();
}

abstract class KintassaTableForm extends KintassaForm {
	/***
	 * instanciates a KintassaTable
	 *
	 * @param $table_name	name of the table
	 * @param $col_map		dictionary of column names and flags
	 * @param $pager		a KintassaPager instance
	 */
	function KintassaTableForm($form_name, $col_map, $pager, $title = null) {
		parent::KintassaForm($form_name);

		$this->col_map = $col_map;
		$this->pager = $pager;

		if ($title == null) {
			$this->title = $form_name;
		} else {
			$this->title = $title;
		}
	}

	function render() {
		$this->begin_table();

		$this->begin_header();
		$this->end_header();

		$this->begin_footer();
		$this->end_footer();

		foreach ($this->visible_rows() as $row) {
			$this->begin_row($row_id);
			foreach ($this->cols as $col) {
				$this->begin_col($col_id);
				$this->end_col($col_id);
			}
			$this->end_row($row_id);
		}

		$this->end_table();
	}

	function begin_table() {
		echo("<table>");
	}

	function end_table() {
		echo("</table>");
	}

	function begin_header() {
		echo("<thead>");
		echo("<tr>");
		foreach ($this->col_map as $k) {
			echo("<th>{$k}</th>");
		}
		echo("</tr>");
	}

	function end_header() {
		echo("</thead>");
	}

	function begin_footer() {
		echo("<tfoot>");
	}

	function end_footer() {
		echo("</tfoot>");
	}

	function begin_row($row_id) {
		$this->row_id = $row_id;
		echo("<tr>");
	}

	function end_row($row_id) {
		$this->row_id = null;
		echo("</tr>");
	}

	function begin_col($col_id) {
		echo("<td>");
	}

	function end_col($col_id) {
		echo("</td>");
	}

	function visible_rows() {
		return $this->pager->items_on_page();
	}
}

abstract class KintassaRowFormFactory {
	function KintassaRowFormFactory() {}
	abstract function instanciate($table_form, $row_id);
}

abstract class KintassaRowOptionsTableForm extends KintassaTableForm {
	function KintassaRowOptionsTableForm($table_name, $col_map, $pager, $row_form_factory) {
		parent::KintassaTableForm($table_name, $col_map, $pager);

		$this->row_form_factory = $row_form_factory;

		// add options column for edit/delete/sort/clone buttons
		$this->col_map[] = "Options";

		// generate option forms for each row
		$this->row_forms = $this->generate_row_forms();
	}

	function handle_forms() {
		return False;
	}

	function begin_header() {
		$this->handle_forms();
		parent::begin_header();
	}

	function generate_row_forms() {
		$rows = $this->pager->items_on_page();
		$row_forms = array();

		foreach ($rows as $row) {
			$row_forms[$row] = $this->row_form_factory->instanciate($this, $row);
		}

		return $row_forms;
	}

	function begin_col($col_id) {
		parent::begin_col($col_id);
		$row_forms[$this->row_id]->render();
	}

	function handle_submissions() {
		foreach ($this->row_forms as $form) {
			if ($form->have_submission()) {
				$row_id = $form->get_var('row_id');
				$act = $form->submitted_action();
				$action_handler = 'do_row_action_' . $act;
				$this->$action_handler($row_id);
				$got_submission = true;
			}
		}
	}
}

?>