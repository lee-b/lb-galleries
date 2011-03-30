<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('micro_orm.php');

class KintassaGallery extends KintassaMicroORMObject {
	function KintassaGallery($id = null) {
		$this->id = $id;
	}

	function save() {
		// TODO: Not implemented
	}
	
	function load() {
		// TODO: Not implemented
	}

	function render($width = null, $height = null) {
		if (!$this->is_loaded()) {
			$this->load();

			$gallery_code = "<div class=\"kintassa_gallery\"";

			$style_code = "";
			if ($width) { $style_code .= "width: {$width};"; }
			if ($height) { $style_code .= "height: {$height};"; }
			if (strlen($style_code) > 0) { $gallery_code .= " style=\"{$style_code}\""; }
			
			$gallery_code .= ">GALLERY NUMBER {$this->id}</div>";
			
			return $gallery_code;
		}
	}
}

?>