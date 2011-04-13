<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaFilteredImage {
	function __construct($orig_path) {
		$this->orig_path = $orig_path;
	}

	function filtered_path($cache_root, $args) {
		if ($this->orig_matches($args)) {
			return $this->orig_path;
		}

		$pathparts = pathinfo($this->orig_path);

		$dirname = $pathparts['dirname'];
		$basename = $pathparts['basename'];
		$real_basename = basename($basename,'.'.$pathparts['extension']);
		$ext = $pathparts['extension'];

		$filter_name = get_class($this);
		$prefix = "__flt_" . $filter_name . "_";
		$args_str = $this->encode_args($args);

		$FS = DIRECTORY_SEPARATOR;

		return  "{$cache_root}" . $FS . "{$real_basename}{$prefix}{$args_str}.{$ext}";
	}

	/***
	 * checks for cached image and generates it if necessary
	 */
	function ensure_cached($filtered_path, $args) {
		if (realpath($filtered_path) == realpath($this->orig_path)) {
			// no filter applied; early exit
			return true;
		}

		if (!file_exists($filtered_path)) {
			$res = $this->filter_image($this->orig_path, $filtered_path, $args);
			if (!$res) {
				echo("Failed to filter image");
				return false;
			}
		}

		return true;
	}

	abstract function orig_matches($args);
	abstract function encode_args($args);
	abstract function filter_image($orig_path, $output_path, $args);
}

class KintassaResizeableImage extends KintassaFilteredImage {
	function orig_matches($args) {
		$w = $args['width'];
		$h = $args['height'];

		$imgsize = getimagesize($this->orig_path);
		if (!$imgsize) {
			return false;
		}
		$imgsize = $imgsize[3];

		if ($imgsize['width'] != $w) return false;
		if ($imgsize['height'] != $h) return false;

		return true;
	}

	function encode_args($args) {
		$w = $args['width'];
		$h = $args['height'];
		return "{$w}x{$h}";
	}

	function filter_image($orig_path, $output_path, $args) {
		$new_w = $args['width'];
		$new_h = $args['height'];

		$img = KintassaUtils::load_image($orig_path);
		$new_img = KintassaUtils::resample_image($img, $new_w, $new_h);
		KintassaUtils::save_image($new_img, $output_path);

		KintassaUtils::destroy_image($img);
		KintassaUtils::destroy_image($new_img);

		return true;
	}
}

class KintassaImageFinder {
	function __construct($cache_root) {
		$this->cache_root = $cache_root;
	}
}

abstract class KintassaMappedImageFinder extends KintassaImageFinder {
	abstract function uri_from_id($id);
	abstract function image_path_from_id($id);
}

?>