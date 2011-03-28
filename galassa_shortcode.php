<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once "galassa_gallery.php";

class GalassaShortcode {
	function GalassaShortcode() {
		add_shortcode('kintassa_gallery', array(&$this, 'render_shortcode'));
		$this->register_stylesheets();
	}

	function register_stylesheets() {
        $myStyleUrl = plugins_url('/stylesheets/kintassa_gallery.css', __FILE__);
	    wp_register_style('kintassa_gallery', $myStyleUrl);
        wp_enqueue_style('kintassa_gallery');
	}

	function render_shortcode($atts) {
		$known_attribs = array(
			"id" => null,
		);
		$parsed_atts = shortcode_atts(&$known_attribs, $atts);
		$id = $parsed_atts['id'];

		$gal = new GalassaGallery($id);
		$rendered_gallery = $gal->render();
		
		return $rendered_gallery;
	}
}

?>