<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/
?>
<div id="<?php echo $unique_id; ?>-wrapper" <?php echo $cls . " " . $sty; ?>>
	<div id="nav">
		<a href="#"><span id="prev">Prev</span></a>
		<a href="#"><span id="next">Next</span></a>
	</div>
	<div id="<?php echo $unique_id; ?>" <?php echo $cls . " " . $sty; ?>>
		<?php
			$images = $gallery->images();
			$first = true;

			foreach($images as $img) {
				if ($first) {
					$cls = " class=\"first-item\"";
					$first = false;
				} else {
					$cls = "";
				}

				?>
				<img <?php echo $cls; ?>
					width="<?php echo $gallery->width; ?>"
					height="<?php echo $gallery->height; ?>"
					src="<?php echo $this->image_uri($img); ?>"
					title="<?php echo $img->name; ?>"
				>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	jQuery(function() {
	    jQuery('#<?php echo $unique_id; ?>').cycle({
	        fx:      'scrollHorz',
	        timeout:  0,
	        speed:	 300,
	        prev:    '#prev',
	        next:    '#next'
		});
	});
</script>