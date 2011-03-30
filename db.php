<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa. 
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

namespace kintassa\wp_gallery;

require_once("micro_orm.php");

$gallery_tbl_name = $wpdb->prefix . "kintassa_gallery";
$images_tbl_name = $wpdb->prefix . "kintassa_gal_img";

private function table_exists($tablename) {
	return ($wpdb->get_var("show tables like '$wp_track_members_table'") != $tablename);
}

private function create_tables() {
	global $wpdb;

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
		CREATE  TABLE ${images_tbl_name} (
		  `id` INT NOT NULL AUTO_INCREMENT ,
		  `sort_pri` INT NULL DEFAULT 0 ,
		  `filepath` VARCHAR(4096) NULL ,
		  `title` VARCHAR(255) NULL ,
		  `description` VARCHAR(255) NULL ,
		  `gallery_id` INT NOT NULL,
		  PRIMARY KEY (`id`) ,
		  INDEX `gallery_id` () ,
		  CONSTRAINT `gallery_id`
		    FOREIGN KEY ()
		    REFERENCES {$gallery_tbl_name} ()
		    ON DELETE NO ACTION
		    ON UPDATE NO ACTION
		)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_unicode_ci;
SQL;
	
	if (!table_exists($gallery_tbl_name)) {
		$wpdb->query($gallery_tbl_sql);
	}

	if (!table_exists($images_tbl_name)) {
		$wpdb->query($images_tbl_sql);
	}
}

function setup_db() {
	wp_set_option("kintassa_gallery_dbver", "1.0");
	create_tables();
}

?>