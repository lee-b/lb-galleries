<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once('kin_micro_orm.php');

class KintassaGallery extends KintassaMicroORMObject {
	function KintassaGallery($id = null) {
		parent::KintassaMicroORMObject($id);
		
		if (!$this->is_loaded()) {
			$this->name = "";
			$this->width = 320;
			$this->height = 200;
		}
	}

	static function table_name() {
		global $wpdb;
		return $wpdb->prefix . "kintassa_gallery";
	}
	
	function save() {
		global $wpdb;
		
		if (!ISSET($this->id)) {
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
	
	function load($id) {
		global $wpdb;
		
		assert ($this->id != null);
		$row = $wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE id={$this->id}");
		
		$this->name = $row->name;
		$this->width = $row->width;
		$this->height = $row->height;
		$this->id = $id;
	}

	function render($width = null, $height = null) {
		if (!$this->is_loaded()) {
			$this->load();

			$gallery_code = "<div class=\"kintassa_gallery\"";

			$style_code = "";
			if ($width) { $style_code .= "width: {$width};"; }
			if ($height) { $style_code .= "height: {$height};"; }
			if (strlen($style_code) > 0) { $gallery_code .= " style=\"{$style_code}\""; }
			
			$gallery_code .= ">GALLERY NUMBER {$this->id}</div>";
			
			return $gallery_code;
		}
	}
}

?>