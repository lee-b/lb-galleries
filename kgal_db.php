<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kgal_gallery.php");
require_once("kgal_image.php");

global $wpdb;

function kgallery_create_tables() {
	global $wpdb;

	$gallery_tbl_name = KintassaGallery::table_name();

	$gallery_tbl_sql = <<<SQL
		CREATE TABLE {$gallery_tbl_name} (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`name` VARCHAR(128) NULL ,
			`width` INT NULL ,
			`height` INT NULL ,
			`display_mode` VARCHAR(16),
			PRIMARY KEY (`id`)
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_unicode_ci;
SQL;

	$images_tbl_name = KintassaGalleryImage::table_name();

	$images_tbl_sql = <<<SQL
		CREATE  TABLE `{$images_tbl_name}` (
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

	if (!KintassaMicroORMObject::table_exists($gallery_tbl_name)) {
		$wpdb->query($gallery_tbl_sql);
	}

	if (!KintassaMicroORMObject::table_exists($images_tbl_name)) {
		$wpdb->query($images_tbl_sql);
	}
}

function kgallery_setup_db() {
	add_option("kintassa_gallery_dbver", "1.0");
	kgallery_create_tables();
}

?>