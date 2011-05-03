<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_micro_orm.php'));
require_once('kgal_config.php');

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
		global $wpdb;

		$table_name = KintassaGalleryImage::table_name();
		$data = array(
			"sort_pri"		=> $this->sort_pri,
			"filepath"		=> $this->filepath,
			"name"			=> $this->name,
			"mimetype"		=> $this->mimetype,
			"description"	=> $this->description,
			"gallery_id"	=> $this->gallery_id,
		);
		$where = array(
			"id"			=> $this->id,
		);
		$data_fmt = array(
			"%d",
			"%s",
			"%s",
			"%s",
			"%s",
			"%d"
		);
		$where_fmt = array("%d");

		$res = $wpdb->update($table_name, $data, $where, $data_fmt, $where_fmt);
		if ($res != 1) {
			return false;
		}

		return true;
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