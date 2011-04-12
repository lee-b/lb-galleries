<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

// initialise wordpress //////////////////////////////////////////////////////

error_reporting(E_ALL);

function find_parent_file($fn) {
	$current_dir = dirname(__file__);

	while (realpath($current_dir) != "/") {
		$current_dir = $current_dir . "/..";
		$fn_path = $current_dir . "/" . $fn;
		if (file_exists($fn_path)) {
			return realpath($fn_path);
		}
	}

	return null;
}

$wp_load = find_parent_file("wp-load.php");
if(!$wp_load) {
	exit("Couldn't locate wordpress!");
}
require_once($wp_load);

// real code starts here /////////////////////////////////////////////////////

require_once("../kgal_image_finder.php");
require_once("../kgal_image.php");
require_once("../kgal_gallery.php");
require_once("../kin_utils.php");

function send_gallery_image_by_id($id) {
	$upload_path = '/vweb/kintassa_wpscratch/webroot/wp-content/plugins/kintassa_gallery/uploads';
	$cache_path = '/vweb/kintassa_wpscratch/webroot/wp-content/plugins/kintassa_gallery/cache';

	// load basic image details from db
	$img = new KintassaGalleryImage($id);
	if ($img->is_dirty()) {
		exit("ERROR: Couldn't load gallery image: $id");
	}
	$ctype = $img->mime_type();

	// find/generate a version of the image that's scaled correctly for this
	// gallery
	$finder = new KGalImageFinder($upload_path, $cache_path);
	$path = $finder->image_path_from_id($id);
	if (!$path) {
		exit("ERROR: Couldn't locate image file for image id $id");
	}

	// render results
	header("Content-type: {$ctype}");
	readfile($path);
}

$id = $_GET['id'];
assert(KintassaUtils::isInteger($id));
send_gallery_image_by_id($id);

?>