<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaWPPlugin {
	function KintassaWPPlugin($filepath) {
		global $wpdb;

		register_activation_hook($filepath, array(&$this, 'install'));
		register_deactivation_hook($filepath, array(&$this, 'remove'));
	}

	function log($msg) {
		if (defined(WP_DEBUG) && WP_DEBUG==true) {
			print_r($msg);
		}
	}

	abstract function install();
	abstract function remove();
}

?>
