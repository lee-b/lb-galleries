<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_page.php");

class KGalleryAboutPage extends KintassaPage {
	function content() {
		echo <<<HTML
	<p>Copyright &copy; 2011 <a href="http://www.kintassa.com/">Kintassa</a>.
	All rights reserved.</p>
HTML;
	}
}

?>