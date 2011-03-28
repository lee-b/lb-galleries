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

require_once("galassa_menu.php");
$galassa_menu = new GalassaMenu("Galassa");

require_once("galassa_shortcode.php");
$galassa_body_filter = new GalassaShortcode();

?>