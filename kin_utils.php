<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

class KintassaUtils {
	static function isInteger($val) {
		return (preg_match('@^[-]?[0-9]+$@',$val) === 1);
	}

	static function admin_path($plugin, $panel, $panel_args) {
		$base_uri = admin_url() . "?page={$plugin}_{$panel}";

		$args = "";
		foreach($panel_args as $key => $val) {
			$args .= "&" . $key . "=" . $val;
		}

		return $base_uri . $args;
	}

	static function load_image($fname) {
		$res = array();

		$imgsize = getimagesize($fname);
		if (!$imgsize) {
			return null;
		}

		$res['width'] = $imgsize[0];
		$res['height'] = $imgsize[1];
		$res['mimetype'] = $imgsize['mime'];

		$loader_map = array(
			"image/jpeg"		=> "imagecreatefromjpeg",
			"image/png"			=> "imagecreatefrompng",
			"image/gif"			=> "imagecreatefromgif"
		);

		$loader = $loader_map[$res['mimetype']];

		$img = @$loader($fname);
		if ($img == null) {
			return null;
		}

		$res['image'] = $img;

		return $res;
	}

	static function save_image($img, $fname) {
		$saver_map = array(
			"image/jpeg"		=> "imagejpeg",
			"image/png"			=> "imagepng",
			"image/gif"			=> "imagegif",
		);

		$saver = $saver_map[$img['mimetype']];
		$real_img = $img['image'];
		$saver($real_img, $fname);
	}
}

?>