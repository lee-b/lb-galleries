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
	$current_dir = dirname($_SERVER['SCRIPT_FILENAME']);
	$last_dir = null;

	while (true) {
		$file_path = $current_dir . DIRECTORY_SEPARATOR . $fn;
		if (file_exists($file_path)) {
			return $file_path;
		}

		$real_dir = realpath($current_dir);
		if ($real_dir == $last_dir) {
			/*	we're not ascending any more, so we
			 * 	must be at the root dir, with nothing
			 *	yet found
			*/
			return null;
		}

		$current_dir = dirname($current_dir);
		$last_dir = $real_dir;
	}
}

$wp_load = find_parent_file("wp-load.php");
if(!$wp_load) {
	exit("Couldn't locate wordpress!");
}
require_once($wp_load);

// real code starts here /////////////////////////////////////////////////////

require_once(".." . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "kgal_config.php");
require_once(kintassa_core('kin_utils.php'));
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "src/kgal_image_finder.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "src/kgal_image.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . "src/kgal_gallery.php");

function send_gallery_image_by_id($id) {
	// load basic image details from db
	$img = new KintassaGalleryImage($id);
	if ($img->is_dirty()) {
		exit("ERROR: Couldn't load gallery image: $id");
	}
	$ctype = $img->mime_type();

	// find/generate a version of the image that's scaled correctly for this
	// gallery
	$finder = new KGalImageFinder(KGAL_CACHE_PATH);
	$path = $finder->image_path_from_id($id);
	if (!$path) {
		exit("ERROR: Couldn't locate image file for image id $id");
	}

	// render results
	header("Content-type: {$ctype}");
	readfile($path);
}

if (isset($_GET['id'])) {
	$id = $_GET['id'];
	assert(KintassaUtils::isInteger($id));
	send_gallery_image_by_id($id);
} else {
	header("HTTP/1.0 404 Not found");
	header("Status: 404 Not found");
	echo("<html><head><title>Not found</title><body>The requested image doesn't exist (any longer?)</body></html>");
	echo("The requested image doesn't exist (any longer?)");
}

?>