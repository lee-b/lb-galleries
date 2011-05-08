<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once('kgal_gallery.php');

/***
 * publically callable function for rendering galleries in templates
 */
function kintassa_gallery($gallery_id, $width=null, $height=null) {
	$gal = new KintassaGallery($gallery_id);
	$rendered_html = $gal->render($width, $height);
	return($rendered_html);
}

?>
