<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kintassa_micro_orm.php');

class GalassaGallery extends KintassaMicroORMObject {
	function GalassaGallery($id = null) {
		$this->id = $id;
	}

	function save() {
		// TODO: Not implemented
	}
	
	function load() {
		// TODO: Not implemented
	}

	function render() {
		if (!$this->is_loaded()) {
			$this->load();
			return "<div class=\"kintassa_gallery\">GALLERY NUMBER {$this->id}</div>";
		}
	}
}

?>