<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kin_form.php");
require_once("kin_page.php");

abstract class KintassaPager {
	function __construct() {}

	function num_pages() {
		$num_results = $this->num_results();
		if ($num_results <= 1) return 1;

		$page_size = $this->page_size();
		$num_pages = (int) ($num_results / $page_size);

		if ($num_results % $page_size) {
			$num_pages += 1;
		}

		return $num_pages;
	}

	abstract function current_page();
	abstract function num_results();
	abstract function page_size();
	abstract function items_on_page();
	abstract function render_page_nav();

	// These methods really make the class more of a table wrapper than
	// a simple pager.  Should probably rename / refactor at some point.
	abstract function delete($row_id);
	abstract function sort_up($row_id);
	abstract function sort_down($row_id);
}

abstract class KintassaTableForm extends KintassaForm {
	/***
	 * @param $table_name	name of the table
	 * @param $col_map		dictionary of column names and flags
	 * @param $pager		a KintassaPager instance
	 */
	function __construct($form_name, $col_map, $pager, $title = null) {
		parent::__construct($form_name);

		foreach ($col_map as $col_key=>$col_val) {
			assert(!KintassaUtils::isInteger($col_key));
		}

		$this->col_map = $col_map;
		$this->pager = $pager;

		if ($title == null) {
			$this->title = $form_name;
		} else {
			$this->title = $title;
		}

		$this->row = null;
	}

	function begin_render($as_sub_el = false) {
//		parent::begin_render($as_sub_el); // TODO: refactor so OK to call

		$this->begin_table();

		$this->begin_header();
		$this->end_header();

		$this->begin_footer();
		$this->end_footer();

		foreach ($this->visible_rows() as $row) {
			$this->begin_row($row);
			foreach ($this->col_map as $col_key => $col_name) {
				$this->begin_col($col_key);
				$this->end_col($col_key);
			}
			$this->end_row($row);
		}
	}

	function end_render($as_sub_el = false) {
		$this->end_table();
//		parent::end_render($as_sub_el); // TODO: refactor so OK to call
	}

	function classes() {
		$cl = parent::classes();
		$cl[] = "widefat";
		return $cl;
	}

	function begin_table() {
		$name = $this->name;
		$cl = $this->class_attrib_str();
		echo("<table id=\"{$name}\" {$cl}>");
	}

	function end_table() {
		echo("</table>");
	}

	function show_col($col_name) {
		return array_key_exists($col_name, $this->col_map);
	}

	function pretty_col_name($col_name) {
		$v = $this->col_map[$col_name];
		if ($v != null) {
			return $v;
		} else {
			return $col_name;
		}
	}

	function begin_header() {
		echo("<thead>");
		foreach ($this->col_map as $col_key => $col_name) {
			echo("<th>");

			if (!$this->show_col($col_key)) {
				echo("no_show: $col_key");
				print_r($this->col_map);
				continue;
			}

			$pretty_name = $this->pretty_col_name($col_key);
			echo("{$pretty_name}");

			echo("</th>");
		}
		echo("</tr>");
	}

	function end_header() {
		echo("</thead>");
	}

	function begin_footer() {
		echo("<tfoot>");
		echo("<tr>");
		foreach ($this->col_map as $col_name) {
			if (!$this->show_col($col_name)) continue;
			$pretty_name = $this->col_name($col_name);
			echo("<th>{$pretty_name}</th>");
		}
		echo("</tr>");
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

abstract class KintassaRowForm extends KintassaForm {
}

abstract class KintassaRowFormFactory {
	function __construct() {}
	abstract function instanciate($table_form, $row_id);
}

abstract class KintassaOptionsTableForm extends KintassaTableForm {
	function __construct($table_name, $col_map, $pager, $row_form_factory) {
		parent::__construct($table_name, $col_map, $pager);

		$this->row_form_factory = $row_form_factory;

		// add options column for edit/delete/sort/clone buttons
		$this->col_map["options"] = "Options";

		$this->process_actions();

		// generate option forms for each row
		$this->row_forms = $this->generate_row_forms();
	}

	abstract function process_actions();

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
		if (!$this->show_col($col)) return;

		if ($col == 'options') {
			$o = $this->row_forms[$this->row->id];
			$as_subform = false; // naming for clarity
			$o->render($as_subform);
		} else {
			echo($this->row->$col);
		}
	}

	function end_col($col) {
		if (!$this->show_col($col)) return;
	}

	/***
	 * returns the button submitted and the form it was submitted on
	 */
	function buttons_submitted($btns) {
		foreach ($btns as $btn) {
			if (isset($_POST[KintassaPageElement::static_prefix . $btn])) {
				$row_id = intval($_POST[KintassaPageElement::static_prefix . 'row_id']);
				return array($btn, $row_id);
			}
		}
	}
}

?>