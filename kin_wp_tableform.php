<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_micro_orm.php");
require_once("kin_wp_form.php");

abstract class KintassaWPTableForm extends KintassaWPForm {
	const RowAdd = 1;
	const RowDel = 2;
	const RowEdit = 4;
	const AllOpts = 7;

	function KintassaWPTableForm($form_name, $kORMClass, $serial = null, $fields = null, $filter = null, $flags = KintassaWPTableForm::AllOpts) {
		parent::KintassaWPForm($form_name, $serial);
		
		$this->kORMClass = $kORMClass;
		$this->fields = $fields;
		$this->filter = $filter;
		$this->flags = $flags;
	}

	function render_rows() {
		// TODO: render rows
		print_r($rows);
	}

	function render_no_records() {
		echo "(No records found)";
	}

	function render_header() {
		$this->begin_form();
	}
	
	function render_footer() {
		$this->render_button("AddNew", "Add New");
		$this->end_form();
	}

	function render_list() {
		$this->render_header();
		
		$cls = $this->kORMClass;
		$rows = KintassaMicroORMObject::get_rows($cls::table_name());
		
		if ($rows == 0) {
			$this->render_no_records();
		} else {
			$this->render_rows();
		}

		$this->render_footer();
	}

	abstract function render_add_new_form();
	abstract function render_add_new_results();

	function handle_add_new_form() {
		// TODO: detect completed form and call render_add_new_results()
		$this->render_add_new_form();
	}
	
	function execute() {
		// TODO: add fields, filter

		if ($this->have_submission("AddNew")) {
			$this->handle_add_new_form();
		} else {
			$this->render_list();
		}
	}
}

?>