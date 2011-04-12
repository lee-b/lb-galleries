<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kin_micro_orm.php');

class KintassaGalleryImage extends KintassaMicroORMObject {
	function init() {
		$this->sort_pri = null;
		$this->filepath = null;
		$this->name = null;
		$this->mimetype = null;
		$this->description = null;
		$this->gallery_id = null;
	}

	static function table_name() {
		global $wpdb;
		return $wpdb->prefix . "kintassa_gal_img";
	}

	function save() {
		// TODO: Not implemented
		assert(false);
		return false;
	}

	function load() {
		global $wpdb;

		assert($this->id != null);

		$table_name = $this->table_name();
		$qry = "SELECT sort_pri,filepath,name,mimetype,description,gallery_id FROM `{$table_name}` WHERE `id`={$this->id};";
		$res = $wpdb->get_row($qry);
		if (!$res) {
			return false;
		}

		$this->sort_pri = $res->sort_pri;
		$this->filepath = $res->filepath;
		$this->name = $res->name;
		$this->mimetype = $res->mimetype;
		$this->description = $res->description;
		$this->gallery_id = $res->gallery_id;

		return true;
	}

	function file_path() {
		return $this->filepath;
	}

	function mime_type() {
		return $this->mimetype;
	}

	function gallery_id() {
		return $this->gallery_id;
	}
}

?>