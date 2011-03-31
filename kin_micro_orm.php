<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaMicroORMObject {
	var $loaded = false;
	var $saved = false;

	function KintassaMicroORMObject($id = null) {
		if ($id) {
			$this->load($id);
		}
	}

	function is_saved() {
		return $this->saved;
	}

	function is_loaded() {
		return $this->loaded;
	}

	abstract protected function save();
	abstract protected function load();
}

?>