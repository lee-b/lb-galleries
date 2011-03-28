<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

class GalassaBodyFilter {
	function GalassaBodyFilter() {
		$this->tag = 'galassa_gallery';
		add_filter('the_content', array(&$this, 'filter_body'));
	}

	function generate_gallery_code() {
		return "<div>GALLERY HERE</div>";
	}
	
	function filter_body($orig) {
		$tag_str = "{" . $this->tag . "}";
		$gallery_code = $this->generate_gallery_code();

		if (strstr($orig, $tag_str)) {
			$res = str_ireplace($tag_str, $gallery_code, $orig);
		} else {
			$res = $orig . $gallery_code;
		}

		return $res;
	}
}

?>