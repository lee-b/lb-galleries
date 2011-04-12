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
	function __construct() {
		parent::__construct(__FILE__);

		require_once("kgal_menu.php");
		$kgallery_menu = new KGalleryMenu();

		require_once("kgal_shortcode.php");
		$kgallery_shortcode = new KGalleryShortcode();

		add_action('init', array($this, 'install_scripts'));
	}

	function reg_script($name, $relpath) {
		$abs_url = plugins_url("scripts" . DIRECTORY_SEPARATOR . $relpath, __file__);
		wp_register_script($name, $abs_url, false, null);
	}

	function install_scripts() {
		$this->reg_script("jquery_cycle_lite", "jquery.cycle.lite.min.js");

		wp_enqueue_script("jquery");
		wp_enqueue_script("jquery_cycle_lite");
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