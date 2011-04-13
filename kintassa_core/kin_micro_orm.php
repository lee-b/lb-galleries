<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

abstract class KintassaMicroORMObject {
	function __construct($id = null) {
		$this->id = $id;
		$this->dirty = false;

		$this->init();

		if ($id != null) {
			$this->dirty = !($this->load($id));
		}
	}

	static function table_exists($table_name) {
		global $wpdb;
		return ($wpdb->get_var("show tables like '{$table_name}'") == $table_name);
	}

	static function get_rows($tbl, $fields = null, $filter = null) {
		global $wpdb;

		$qry = "SELECT ";

		if ($fields != null) {
			$qry .= implode(",", $fields);
		} else {
			$qry .= "*";
		}

		$qry .= " FROM `" . $tbl . "`";

		if ($filter != null) {
			$qry .= " WHERE " . $filter;
		}

		$res = $wpdb->get_results($qry);

		return $res;
	}

	function is_dirty() {
		return $this->dirty;
	}

	function init() {}

	abstract protected function save();
	abstract protected function load();
}

?>