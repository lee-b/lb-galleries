<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_micro_orm.php");

function kgallery_table_exists($tablename) {
	global $wpdb;
	return ($wpdb->get_var("show tables like '{$tablename}'") == $tablename);
}

function kgallery_create_tables() {
	global $wpdb;

	$gallery_tbl_name = $wpdb->prefix . "kintassa_gallery";
	$images_tbl_name = $wpdb->prefix . "kintassa_gal_img";
	
	global $kPlugin;
	
	$gallery_tbl_sql = <<<SQL
		CREATE  TABLE {$gallery_tbl_name} (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`name` VARCHAR(128) NULL ,
			`width` INT NULL ,
			`height` INT NULL ,
			PRIMARY KEY (`id`)
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_unicode_ci;
SQL;
	
	$images_tbl_sql = <<<SQL
		CREATE  TABLE `${images_tbl_name}` (
		  `id` INT NOT NULL AUTO_INCREMENT ,
		  `sort_pri` INT NULL DEFAULT 0 ,
		  `filepath` VARCHAR(4096) NULL ,
		  `title` VARCHAR(255) NULL ,
		  `description` VARCHAR(255) NULL ,
		  `gallery_id` INT NOT NULL,
		  PRIMARY KEY (`id`)
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_unicode_ci;
SQL;
	
	$kPlugin->log("Checking if {$gallery_tbl_name} exists");
	if (!kgallery_table_exists($gallery_tbl_name)) {
		$kPlugin->log("Creating {$gallery_tbl_name}");
		$wpdb->query($gallery_tbl_sql);
	}

	$kPlugin->log("Checking if {$images_tbl_name} exists");
	if (!kgallery_table_exists($images_tbl_name)) {
		$kPlugin->log("Creating {$images_tbl_name}");
		$wpdb->query($images_tbl_sql);
	}
}

function kgallery_setup_db() {
	add_option("kintassa_gallery_dbver", "1.0");
	kgallery_create_tables();
}

?>