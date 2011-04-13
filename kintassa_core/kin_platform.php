<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

class KintassaPlatform {
	static function is_wordpress() {
		return isset($wpdb);
	}
}

?>
