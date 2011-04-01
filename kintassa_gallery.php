<?php
/*
Plugin Name: Kintassa Gallery
Plugin URI: http://www.kintassa.com/projects/kintassa_gallery/
Description: A flexible image gallery
Version: 1.0
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa for licensing.
*/

require_once("kin_wp_plugin.php");
require_once("kgal_db.php");

class KintassaGalleryPlugin extends KintassaWPPlugin {
	function KintassaGalleryPlugin() {
		parent::KintassaWPPlugin(__FILE__);

		require_once("kgal_menu.php");
		$kgallery_menu = new KGalleryMenu();

		require_once("kgal_shortcode.php");
		$kgallery_shortcode = new KGalleryShortcode();
	}

	function install() {
		require_once("kgal_db.php");
		kgallery_setup_db();
	}
	
	function remove() {}
}

// instanciate the plugin
$kGalleryPlugin = new KintassaGalleryPlugin();

// register template tags into the global namespace
require_once("kgal_tags.php");

?>