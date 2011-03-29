<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("galassa_gallery.php");

function kintassa_gallery($gallery_id, $width, $height) {
	$gal = new GalassaGallery($gallery_id);
	$rendered_gallery = $gal->render($width, $height);
}

?>