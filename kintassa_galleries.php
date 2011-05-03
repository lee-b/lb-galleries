<?php
/*
Plugin Name: Kintassa Galleries
Plugin URI: http://www.kintassa.com/projects/kintassa_galleries/
Description: A flexible image gallery system
Version: 1.0
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa for licensing.
*/

if (!function_exists('kintassa_core')) {
	echo "This plugin requires Kintassa Core";
} else {
	function kin_gal_inc($subpath) {
		$full_subpath = "src/" . $subpath;
		if (DIRECTORY_SEPARATOR == "/") {
			$real_subpath = $full_subpath;
		} else {
			$real_subpath = str_replace("/", DIRECTORY_SEPARATOR, $full_subpath);
		}
		return dirname(__file__) . DIRECTORY_SEPARATOR . $real_subpath;
	}

	require_once(kintassa_core('kin_wp_plugin.php'));
	require_once(kin_gal_inc('kgal_config.php'));
	require_once(kin_gal_inc('kgal_db.php'));
	require_once(kin_gal_inc('kgal_menu.php'));
	require_once(kin_gal_inc('kgal_shortcode.php'));
	require_once(kin_gal_inc('kgal_db.php'));
	require_once(kin_gal_inc('kgal_tags.php'));
	
	class KintassaGalleryPlugin extends KintassaWPPlugin {
		function __construct() {
			parent::__construct(__file__);
	
			$kgallery_menu = new KGalleryMenu();
			$kgallery_shortcode = new KGalleryShortcode();
	
			add_action('init', array($this, 'install_scripts'));
	
			$write_dirs = array(KGAL_UPLOAD_PATH, KGAL_CACHE_PATH);
			foreach ($write_dirs as $wd) {
				if (!file_exists($wd)) {
					exit("ERROR: '$wd' does not exist!");
				}
	
				if (!is_writeable($wd)) {
					exit("ERROR: '$wd' is not writeable!");
				}
			}
		}
	
		function reg_script($name, $relpath) {
			$abs_url = plugins_url("scripts" . DIRECTORY_SEPARATOR . $relpath, __file__);
			wp_register_script($name, $abs_url, false, null);
	        $myStyleUrl = plugins_url('/stylesheets/kintassa_gallery.css', __file__);
		    wp_register_style('kintassa_gallery', $myStyleUrl);
		}
	
		function install_scripts() {
			$this->reg_script("jquery_cycle", "jquery.cycle.all.min.js");
	
			wp_enqueue_script("jquery");
			wp_enqueue_script("jquery_cycle");
	        wp_enqueue_style('kintassa_gallery');
		}
	
		function install() {
			kgallery_setup_db();
		}
	
		function remove() {}
	}

	function remove() {
	}

	// instanciate the plugin
	$kGalleryPlugin = new KintassaGalleryPlugin();
	$kGalleryPlugin->install();
}

?>