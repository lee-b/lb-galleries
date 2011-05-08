<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/
?>
<div id="<?php echo $unique_id; ?>-wrapper" <?php echo $wrapper_cls . " " . $companion_sty; ?>>
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
					width="<?php echo $applet->width(); ?>"
					height="<?php echo $applet->height(); ?>"
					src="<?php echo $this->image_uri($img); ?>"
					title="<?php echo $img->name; ?>"
					alt="<?php echo $img->description ?>"
				>
		<?php } ?>
	</div>
	<div class="caption" <?php echo $companion_sty; ?>><span class="caption-inner"></span></div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery('#<?php echo $unique_id; ?>').cycle({
			fx:			'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
		    after:		function() {
			    jQuery('#<?php echo $unique_id; ?>-wrapper .caption .caption-inner')
				    .html(this.alt);
			}
		});
	});
</script>