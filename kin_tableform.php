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

		$this->row = null;
	}

	function render() {
		$this->begin_table();

		$this->begin_header();
		$this->end_header();

		$this->begin_footer();
		$this->end_footer();

		foreach ($this->visible_rows() as $row) {
			$this->begin_row($row);
			foreach ($this->col_map as $col) {
				$this->begin_col($col);
				$this->end_col($col);
			}
			$this->end_row($row);
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

	function begin_row($row) {
		$this->row = $row;
		echo("<tr>");
	}

	function end_row($row) {
		$this->row = null;
		echo("</tr>");
	}

	function begin_col($col) {
		echo("<td>");
	}

	function end_col($col) {
		echo("</td>");
	}

	function visible_rows() {
		$keys = $this->pager->items_on_page();
		return $keys;
	}
}

abstract class KintassaRowFormFactory {
	function KintassaRowFormFactory() {}
	abstract function instanciate($table_form, $row_id);
}

abstract class KintassaOptionsTableForm extends KintassaTableForm {
	function KintassaOptionsTableForm($table_name, $col_map, $pager, $row_form_factory) {
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
		$row_forms = array();

		$rows = $this->pager->items_on_page();
		foreach ($rows as $row) {
			$rf = $this->row_form_factory->instanciate($this, $row);
			assert($rf != null);
			$row_forms[$row->id] = $rf;
		}

		return $row_forms;
	}

	function begin_col($col) {
		parent::begin_col($col);

		if ($col == 'Options') {
			$o = $this->row_forms[$this->row->id];
			$o->render();
		} else {
			$field_name = str_replace(" ", "_", strtolower($col));
			echo($this->row->$field_name);
		}
	}

	/***
	 * returns the button submitted and the form it was submitted on
	 */
	function buttons_submitted($btns) {
		$parent_has = parent::buttons_submitted($btns);
		if ($parent_has) return $parent_has;

		foreach ($this->row_forms as $subform) {
			$sub_has = $subform->buttons_submitted($btns);
			if ($sub_has) {
				return $sub_has;
			}
		}

		return null;
	}
}

?>