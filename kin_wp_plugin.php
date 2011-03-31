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
		
		$this->log("KintassaWPPlugin: enabling debugging");
		if (defined('WP_DEBUG') and WP_DEBUG == true) {
			error_reporting(E_ALL);
			$wpdb->show_errors();
		}
		
		$this->log("KintassaWPPlugin: ctor starting");
		register_activation_hook($filepath, array(&$this, 'install'));
		register_deactivation_hook($filepath, array(&$this, 'remove'));
		$this->log("KintassaWPPlugin: ctor ending");
	}

	function log($msg) {
		print_r($msg . "\n");
	}

	abstract function install();
	abstract function remove();
}

?>
