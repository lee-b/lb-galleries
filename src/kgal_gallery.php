<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once('kgal_config.php');
require_once(KGAL_ROOT_DIR . DIRECTORY_SEPARATOR . 'kintassa_core/kin_micro_orm.php');
require_once('kgal_image.php');

// load applets
$applets_dir = dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . "applets";
$applets_dir_handle = opendir($applets_dir);
while ($applet_fname = readdir($applets_dir_handle)) {
	if ( (substr($applet_fname, 0, strlen("applet_")) == "applet_") &&
	     (substr($applet_fname, -strlen(".php")) == ".php")
	) {
		$full_path = $applets_dir . DIRECTORY_SEPARATOR . $applet_fname;
		require_once($full_path);
	}
}

class KintassaGallery extends KintassaMicroORMObject {
	static function table_name() {
		global $wpdb;
		return $wpdb->prefix . "kintassa_gallery";
	}

	function save() {
		global $wpdb;

		if (!isset($this->id)) {
			// saving for the first time, so we need to insert a record
			$dat = array("name" => $this->name, "width" => $this->width, "height" => $this->height);
			$dat_fmt = array('%s', '%d', '%d');
			$res = $wpdb->insert($this->table_name, &$dat, &$dat_fmt);
			$this->id = $wpdb->insert_id;
		} else {
			$dat = array("name" => $this->name, "width" => $this->width, "height" => $this->height, "id" => $this->id);
			$dat_fmt = array('%s', '%d', '%d', '%d');
			$where = array("id" => $this->id);
			$res = $wpdb->update($this->table_name, &$dat, &$where, &$dat_fmt);
		}
	}

	function init() {
		$this->name = null;
		$this->width = null;
		$this->height = null;
		$this->display_mode = null;
	}

	function load() {
		global $wpdb;

		assert ($this->id != null);

		$row = $wpdb->get_row("SELECT * FROM `{$this->table_name()}` WHERE id={$this->id}");
		if (!$row) {
			return false;
		}

		$this->name = $row->name;
		$this->width = $row->width;
		$this->height = $row->height;
		$this->display_mode = $row->display_mode;

		return true;
	}

	function render($width = null, $height = null) {
		assert($this->id != null);

		if (!KintassaGalleryApplet::is_valid_applet($this->display_mode)) {
			$this->display_mode = 'invalid';
		}

		$applet_info = KintassaGalleryApplet::applet_info($this->display_mode);
		$applet_class = $applet_info['class'];

		$applet = new $applet_class($this);
		$applet->render();
	}

	function images() {
		global $wpdb;

		$table_name = KintassaGalleryImage::table_name();
		$rows = $wpdb->get_results("SELECT id,sort_pri FROM `{$table_name}` WHERE gallery_id={$this->id} ORDER BY sort_pri,name");

		$images = array();
		foreach ($rows as $row) {
			$img = new KintassaGalleryImage($row->id);
			$images[] = $img;
		}

		return $images;
	}
}

?>