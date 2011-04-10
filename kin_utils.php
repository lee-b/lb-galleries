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

	static function admin_path($plugin, $panel, $panel_args) {
		$base_uri = admin_url() . "?page={$plugin}_{$panel}";

		$args = "";
		foreach($panel_args as $key => $val) {
			$args .= "&" . $key . "=" . $val;
		}

		return $base_uri . $args;
	}
}

?>