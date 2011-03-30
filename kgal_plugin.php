<?php
/*
Plugin Name: Galassa
Plugin URI: http://www.kintassa.com/projects/galassa
Description: A flexible image gallery
Version: 1.0
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_menu.php");
require_once("kgal_shortcode.php");
require_once("kgal_db.php");

$kgallery_menu = new KGalleryMenu();
$kgallery_shortcode = new KGalleryShortcode();

register_activation_hook(__FILE__,'kgallery_install');
register_deactivation_hook(__FILE__, 'kgallery_remove');

function kgallery_install() {
	kgallery_setup_db();
}

function kgallery_remove() {
}

?>