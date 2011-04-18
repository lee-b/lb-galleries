<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once("kgal_config.php");
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . 'kintassa_core/kin_image_filter.php');
require_once("kgal_image.php");
require_once("kgal_gallery.php");

class KGalImageFinder extends KintassaMappedImageFinder {
	function uri_from_id($id) {
		return WP_PLUGIN_URL . "/" . basename(dirname(dirname(__file__))) . "/content/image.php?id={$id}";
	}

	function image_path_from_id($id) {
		$img = new KintassaGalleryImage($id);

		if($img->is_dirty()) {
			echo("Couldn't load image (id: $id)");
			return null;
		}

		$orig_path = $img->file_path();
		$args = array();

		$gallery_id = $img->gallery_id();
		$gal = new KintassaGallery($gallery_id);
		if ($gal->is_dirty()) {
			echo("Couldn't load gallery (id: $gallery_id).");
			return null;
		}

		$args['width'] = $gal->width;
		$args['height'] = $gal->height;

		$orig_img = new KintassaResizeableImage($orig_path);
		$filtered_path = $orig_img->filtered_path($this->cache_root, $args);
		$res = $orig_img->ensure_cached($filtered_path, $args);
		if (!$res) {
			return null;
		}

		return $filtered_path;
	}
}

class KintassaThumbnailFinder extends KintassaImageFinder {
	function __construct($width, $height) {
		parent::__construct(KGAL_CACHE_PATH);
		$this->width = $width;
		$this->height = $height;
	}

	function uri_from_fname($fname) {
		$encoded_path = escapeuri($fname);
		return WP_PLUGIN_URL . "/" . basename(dirname(dirname(__file__))) . "/content/thumb.php?width={$this->width}&height={$this->height}&fname={$encoded_fname}";
	}

	function resized_path_to($full_path) {
		if (!file_exists($full_path)) {
			return null;
		}
		$resizeable_image = new KintassaResizeableImage($full_path);
		$args = array(
			"width"	=>	$this->width,
			"height"=>	$this->height,
		);
		$output_fname = $resizeable_image->filtered_path($this->cache_root, $args);
		$ok = $resizeable_image->ensure_cached($output_fname, $args);
		if (!$ok) {
			return null;
		}
		return $output_fname;
	}
}

?>