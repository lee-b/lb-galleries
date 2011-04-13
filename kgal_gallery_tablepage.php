<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_form.php");
require_once("kgal_gallery.php");
require_once("kin_tableform.php");
require_once("kin_utils.php");

class KGalleryTableForm extends KintassaOptionsTableForm {
	function process_actions() {
		$recognised_actions = array("edit", "del");
		$actions_taken = $this->buttons_submitted($recognised_actions);

		if ($actions_taken) {
			$action = $actions_taken[0];
			$row_id = $actions_taken[1];

			$handler = "do_row_action_" . $action;

			return $this->$handler($row_id);
		}
	}

	function do_row_action_edit($row_id) {
		// dummy, handled on a new page
		return true;
	}

	function do_row_action_del($row_id) {
		// TODO: cascade-delete images in gallery
		if ($this->pager->delete($row_id)) {
			echo ("Gallery #{$row_id} deleted.");
		}
		return false;
	}

	function do_row_action_up($row_id) {
		// dummy, option not available for galleries
		return false;
	}

	function do_row_action_down($row_id) {
		// dummy, option not available for galleries
		return false;
	}

	function handle_submissions() {
		return false;
	}

	function end_table() {
		parent::end_table();
		$this->pager->render_page_nav();
	}
}

class KGalleryRowOptionsForm extends KintassaRowForm {
	const Sort = 1;
	const Edit = 2;
	const Delete = 4;
	const All = 7;

	function __construct($table_form, $row, $opts) {
		$form_name = $table_form->name() . "_row_" . $row->id;
		parent::__construct($form_name);

		$this->row_id_field = new KintassaHiddenField(
			"row_id", $name="row_id", $default_val = $row->id, $non_unique=true
		);
		$this->add_child($this->row_id_field);

		if ($opts & KGalleryRowOptionsForm::Sort) {
			$this->add_child(new KintassaButton("&uarr;", $name="up", $primary=false, $non_unique=true));
			$this->add_child(new KintassaButton("&darr;", $name="down", $primary=false, $non_unique=true));
		}

		if ($opts & KGalleryRowOptionsForm::Edit) {
			$edit_args = array("mode" => "gallery_edit", "id" => $row->id);
			$edit_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $edit_args);
			$edit_btn = new KintassaLinkButton("Edit", $name="edit", $uri = $edit_uri);
			$this->add_child($edit_btn);
		}

		if ($opts & KGalleryRowOptionsForm::Delete) {
			$this->add_child(new KintassaButton("Del", $name="del", $primary=false, $non_unique=true));
		}
	}

	function handle_submissions() {
		// dummy, handled by parent table
		return false;
	}
}

class KGalleryRowOptionsFactory extends KintassaRowFormFactory {
	function __construct($opts) {
		$this->opts = $opts;
	}

	function instanciate($table_form, $row) {
		return new KGalleryRowOptionsForm($table_form, $row, $this->opts);
	}
}

class KintassaGalleryDBResultsPager extends KintassaPager {
	function __construct($table_name, $page_size = 10) {
		parent::__construct();

		assert ($page_size > 0);

		$this->table_name =  $table_name;
		$this->page_size = $page_size;
		$this->results = null;
	}

	function sort_up($row_id) {}
	function sort_down($row_id) {}

	function delete($row_id) {
		global $wpdb;
		$qry = "DELETE FROM `{$this->table_name}` WHERE id={$row_id}";
		return ($wpdb->query($qry) != false);
	}

	function num_results() {
		global $wpdb;
		$qry = $this->build_count_query();
		$res = $wpdb->get_var($qry);
		return $res;
	}

	function page_size() {
		return $this->page_size;
	}

	function current_page() {
		if (isset($_GET['pagenum'])) {
			$pg = (int) $_GET['pagenum'];
			$num_pages = $this->num_pages();

			if ($pg < 1) {
				$pg = 1;
			} else if ($pg > $num_pages) {
				$pg = $num_pages;
			}
		} else {
			$pg = 1;
		}

		return $pg;
	}

	function items_on_page() {
		$db_results = $this->get_db_results();

		$res = array();
		foreach ($db_results as $row) {
			$res[] = $row;
		}
		return $res;
	}

	function page_link($page_num) {
		$page_args = array("mode" => "gallery_list", "pagenum" => $page_num);
		$page_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $page_args);
		return $page_uri;
	}

	function render_page_nav() {
		$num_records = $this->num_results();
		if ($num_records <= 1) return;

		$pages = $this->num_pages();
		$current_page = $this->current_page();

		echo("<div class=\"page-nav\">{$num_records} entries found; {$pages} pages, {$this->page_size} entries per page. Go to page: ");

		foreach (range(1, $pages, 1) as $pg) {
			$page_link = $this->page_link($pg);
			if ($pg == $current_page) {
				echo(" <strong>{$pg}</strong> ");
			} else {
				echo (" <a href=\"{$page_link}\" class=\"button\">{$pg}</a> ");
			}
		}

		echo ("</div>");
	}

	function build_count_query() {
		$qry = "SELECT COUNT(*) FROM {$this->table_name}";
		return $qry;
	}

	function build_page_query() {
		$page_size = $this->page_size();

		$page_num = $this->current_page();
		$page_num -= 1; // count from zero

		$start_item = $page_size * $page_num;
		$qry = "SELECT * FROM {$this->table_name} ORDER BY `name` ASC LIMIT {$start_item},{$page_size}";

		return $qry;
	}

	/***
	 * gets db results if not already cached, then returns them
	 */
	function get_db_results() {
		global $wpdb;

		if ($this->results == null) {
			$qry = $this->build_page_query();
			$this->results = $wpdb->get_results($qry);
		}

		return $this->results;
	}
}

class KGalleryTablePage extends KintassaPage {
	function __construct($name, $title) {
		parent::__construct($title);

		$form_name = $name;

		$col_map = array(
			"id"				=> null,
			"name"				=> "Name",
			"width"				=> "Width",
			"height"			=> "Height",
			"display_mode"		=> "Display Mode"
		);

		$table_name = KintassaGallery::table_name();
		$pager = new KintassaGalleryDBResultsPager($table_name);

		$row_opts = KGalleryRowOptionsForm::Edit | KGalleryRowOptionsForm::Delete;
		$row_form_fac = new KGalleryRowOptionsFactory($row_opts);
		$this->table_form = new KGalleryTableForm($form_name, $col_map, $pager, $row_form_fac);
	}

	function content() {
		$this->table_form->execute();

		$page_args = array(
			"mode" => "gallery_add",
		);
		$page_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $page_args);
		echo("<a href=\"{$page_uri}\" class=\"button\">Add Gallery</a>");
	}
}

?>