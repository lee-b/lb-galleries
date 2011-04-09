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
}

?>