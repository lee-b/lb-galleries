<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

class KintassaUtils {
	static function isInteger($val) {
		return (preg_match('@^[-]?[0-9]+$@',$val) === 1);
	}

	static function plugin_uri() {
		return WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	}

	static function uri_path($rel_uri) {
		$full_uri = KintassaUtils::plugin_uri() . $rel_uri;
		return $full_uri;
	}
}

?>