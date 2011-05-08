<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once('kgal_gallery.php');

class KGalleryShortcode {
	function __construct() {
		add_shortcode('kintassa_gallery', array(&$this, 'render_shortcode'));
	}

	/***
	 * wordpress shortcode handler for kintassa galleries
	 */
	function render_shortcode($atts) {
		$known_attribs = array(
			"id" => null,
		);
		$parsed_atts = shortcode_atts(&$known_attribs, $atts);

		$id = $parsed_atts['id'];

		$gal = new KintassaGallery($id);
		$rendered_gallery = $gal->render();

		return $rendered_gallery;
	}
}

?>