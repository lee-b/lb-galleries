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

function send_thumb($fname, $width, $height) {
	$finder = new KintassaThumbnailFinder($width, $height);

	$thumb_file = $finder->resized_path_to($fname);
	if (!$thumb_file || $thumb_file == null) {
		echo("ERROR: couldn't resize image!");
	}

	// load basic image details from db
	$img_details = getimagesize($thumb_file);
	$mimetype = $img_details['mime'];

	// render results
	header("Content-type: {$mimetype}");
	readfile($thumb_file);
}

$fname = $_GET['fname'];
$width = $_GET['width'];
$height = $_GET['height'];

$ok = (	isset($_GET['fname']) &&
		isset($_GET['width']) &&
		isset($_GET['height']) &&
		KintassaUtils::isInteger($width) &&
		KintassaUtils::isInteger($height) &&
		$width <= 2048 &&
		$height <= 2048
);

$fname = basename($fname); // ensure no parent directory escapes occur
$full_orig_path = KGAL_UPLOAD_PATH . DIRECTORY_SEPARATOR . $fname;

$ok = $ok && file_exists($full_orig_path);

if ($ok) {
	send_thumb($full_orig_path, $width, $height);
} else {
	echo ("ERROR: invalid image request ($full_orig_path)");
/*	header("HTTP/1.0 404 Not found");
	header("Status: 404 Not found");
	echo("<html><head><title>Not found</title><body>The requested image doesn't exist (any longer?)</body></html>");
	echo("The requested image doesn't exist (any longer?)");*/
}

?>