<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_image_filter.php");
require_once("kgal_image.php");
require_once("kgal_gallery.php");

class KGalImageFinder extends KintassaMappedImageFinder {
	function uri_from_id($id) {
		return "/wp-content/plugins/kintassa_gallery/content/image.php?id={$id}";
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

?>