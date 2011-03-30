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

require_once("menu.php");
require_once("shortcode.php");
require_once("db.php");

$menu = new KGalleryMenu("Galassa");
$shortcode = new KGalleryShortcode();

register_activation_hook(__FILE__,'kgallery_install');
register_deactivation_hook(__FILE__, 'kgallery_remove');

function kgallery_install() {
	kgallery_setup_db();
}

function kgallery_remove() {}

?>