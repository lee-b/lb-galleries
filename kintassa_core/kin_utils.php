<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
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

	static function resample_image($orig_img,$thumbnail_width,$thumbnail_height) {
		// NOTE: borrowed and modified from a post on the imagecopyresampled()
		//       wordpress codex page

		$width_orig = $orig_img['width'];
		$height_orig = $orig_img['height'];
		$ratio_orig = $width_orig/$height_orig;

		if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
			$new_height = $thumbnail_width/$ratio_orig;
			$new_width = $thumbnail_width;
		} else {
			$new_width = $thumbnail_height*$ratio_orig;
			$new_height = $thumbnail_height;
		}

		$x_mid = $new_width/2;  //horizontal middle
		$y_mid = $new_height/2; //vertical middle

		$process = imagecreatetruecolor(round($new_width), round($new_height));

		imagecopyresampled($process, $orig_img['image'], 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

		imagedestroy($process);

		$new_img = array();
		$new_img['width'] = $thumbnail_width;
		$new_img['height'] = $thumbnail_height;
		$new_img['image'] = $thumb;
		$new_img['mimetype'] = $orig_img['mimetype'];

		return $new_img;
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

	function destroy_image(&$img) {
		imagedestroy($img['image']);
		$img['image'] = null;
		$img = null;
	}
}

?>