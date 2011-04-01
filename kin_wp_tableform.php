<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_micro_orm.php");

class KintassaWPTableForm {
	const RowAdd = 1;
	const RowDel = 2;
	const RowEdit = 4;
	const AllOpts = 7;

	function KintassaWPTableForm($kORMClass, $fields = null, $filter = null, $flags = KintassaWPTableForm::AllOpts) {
		$this->kORMClass = $kORMClass;
		$this->fields = $fields;
		$this->filter = $filter;
		$this->flags = $flags;
	}
	
	function render($form_name) {
		// TODO: add fields, filter
		$cls = $this->kORMClass;
		$rows = KintassaMicroORMObject::get_rows($cls::table_name());
		print_r($rows);
	}
}

?>