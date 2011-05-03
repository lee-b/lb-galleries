<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_form.php'));
require_once(kintassa_core('kin_tableform.php'));
require_once(kintassa_core('kin_utils.php'));
require_once('kgal_config.php');
require_once('kgal_gallery.php');
require_once('kgal_image.php');

class KGalleryImageTableForm extends KintassaOptionsTableForm {
	function process_actions() {
		$recognised_actions = array("up", "down", "edit", "del");
		$actions_taken = $this->buttons_submitted($recognised_actions);

		if ($actions_taken) {
			$action = $actions_taken[0];
			$row_id = $actions_taken[1];

			$handler = "do_row_action_" . $action;

			return $this->$handler($row_id);
		}
	}

	function do_row_action_edit($row_id) {
		// dummy; this is handled on a new page
	}

	function do_row_action_del($row_id) {
		if (!$this->pager->row_exists($row_id)) {
			exit ("<div class=\"notice\">Row $row_id already deleted.</div>");
		}
		if ($this->pager->delete($row_id)) {
			echo ("<div class=\"notice\">Gallery image #{$row_id} deleted.</div>");
		}
		return false;
	}

	function do_row_action_up($row_id) {
		$this->pager->sort_up($row_id);
	}

	function do_row_action_down($row_id) {
		$this->pager->sort_down($row_id);
	}

	function handle_submissions() {
		return false;
	}

	function end_table() {
		parent::end_table();
		$this->pager->render_page_nav();
	}

	function begin_col($col) {
		if ($col == "filepath") {
			KintassaTableForm::begin_col($col);
			$fname = $this->row->$col;
			$width = 80;
			$height = 80;
			$basename = urlencode(basename($fname));
			$lnk = WP_PLUGIN_URL . "/" . basename(dirname(dirname(__file__))) . "/content/thumb.php?";
			$lnk .= "fname=$basename&width=${width}&height=${height}";
			echo "<img src=\"$lnk\">";
		} else {
			parent::begin_col($col);
		}
	}
}

class KGalleryImageRowOptionsForm extends KintassaRowForm {
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

		if ($opts & KGalleryImageRowOptionsForm::Sort) {
			$this->add_child(new KintassaButton("&uarr;", $name="up", $primary=false, $non_unique=true));
			$this->add_child(new KintassaButton("&darr;", $name="down", $primary=false, $non_unique=true));
		}

		if ($opts & KGalleryImageRowOptionsForm::Edit) {
			$edit_args = array("mode" => "galleryimage_edit", "id" => $row->id);
			$edit_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $edit_args);
			$edit_btn = new KintassaLinkButton("Edit", $name="edit", $uri = $edit_uri);
			$this->add_child($edit_btn);
		}

		if ($opts & KGalleryImageRowOptionsForm::Delete) {
			$this->add_child(new KintassaButton("Del", $name="del", $primary=false, $non_unique=true));
		}
	}

	function handle_submissions() {
		// dummy, handled by parent table
		return false;
	}
}

class KGalleryImageRowOptionsFactory extends KintassaRowFormFactory {
	function __construct($opts) {
		$this->opts = $opts;
	}

	function instanciate($table_form, $row) {
		return new KGalleryImageRowOptionsForm($table_form, $row, $this->opts);
	}
}

class KintassaGalleryImageDBResultsPager extends KintassaPager {
	const RowSpace = 2;
	const RowJump = 3;

	function __construct($table_name, $page_size = 10, $gallery_id = null) {
		parent::__construct();

		assert ($page_size > 0);
		assert($gallery_id != null);

		$this->table_name =  $table_name;
		$this->page_size = $page_size;
		$this->gallery_id = $gallery_id;
		$this->results = null;
	}

	private function modify_sort($row_id, $sort_delta) {
		$gal = new KintassaGalleryImage($row_id);
		if ($gal->is_dirty()) {
			// failed to load from db; probably deleted now due to different
			// browser windows being out of sync, so just ignore the request
			// and let the updated table show the user what's now available.
			return false;
		}
		$gal->sort_pri += $sort_delta;
		$gal->save();

		$this->reorder();

		return true;
	}

	function row_exists($row_id) {
		global $wpdb;
		$table_name = KintassaGalleryImage::table_name();
		$qry = "SELECT id from {$table_name} WHERE id={$row_id}";
		$res = $wpdb->get_results($qry);
		return ($res != false);
	}

	function sort_up($row_id) {
		assert($this->modify_sort($row_id, -KintassaGalleryImageDBResultsPager::RowJump) != false);
	}

	function sort_down($row_id) {
		assert($this->modify_sort($row_id, KintassaGalleryImageDBResultsPager::RowJump) != false);
	}

	private function reorder() {
		global $wpdb;

		$table_name = KintassaGalleryImage::table_name();
		$gallery_id = $this->gallery_id;

		@mysql_query("BEGIN", $wpdb->dbh);

		$qry = "SELECT id FROM wp_kintassa_gal_img WHERE gallery_id={$this->gallery_id} ORDER BY sort_pri ASC,name ASC";
		$rows = $wpdb->get_results($qry);
		if (!$rows) {
			echo("error running query");
		}

		$pri = 0;
		foreach ($rows as $row) {
			$update_qry = "UPDATE `{$table_name}` SET `sort_pri`={$pri} WHERE `id`={$row->id}";
			$res = mysql_query($update_qry, $wpdb->dbh);
			if (!$res) {
				$wpdb->print_error();
			}

			$pri += KintassaGalleryImageDBResultsPager::RowSpace;
		}

		@mysql_query("COMMIT", $wpdb->dbh);
	}

	private function get_filenames_for_row($row_id) {
		global $wpdb;
		$fnames = array();

		// original upload's filename
		$table_name = KintassaGalleryImage::table_name();
		$qry = "SELECT `filepath` FROM `{$table_name}` WHERE id={$row_id}";
		$fname = $wpdb->get_var($qry);
		$fnames[] = $fname;

		if (file_exists($fname)) {
			$path_parts = pathinfo($fname);
			$fullname = $path_parts['basename'];
			if (isset($path_parts['extension'])) {
				$exten = $path_parts['extension'];
			} else {
				$exten = "";
			}
			$basename = substr($fullname, 0, strlen($fullname) - strlen($exten));
			$prefix = $basename . "__flt_";

			// cached version filenames
			$cache_dir_handle = opendir(KGAL_CACHE_PATH);
			while ($fname = readdir($cache_dir_handle)) {
				if (substr($fname, 0, strlen($prefix)) == $prefix) {
					$fnames[] = $fname;
				}
			}
		}

		return $fnames;
	}

	function delete($row_id) {
		global $wpdb;

		// get list of files to remove
		$fnames = $this->get_filenames_for_row($row_id);

		// remove from db
		$qry = "DELETE FROM `{$this->table_name}` WHERE id={$row_id}";
		$res = $wpdb->query($qry);

		// remove files
		foreach($fnames as $fname) {
			if (file_exists($fname)) {
				@unlink($fname);
			}
		}

		return ($res != false);
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
		$page_args = array("mode" => "gallery_edit", "pagenum" => $page_num, "id" => $this->gallery_id);
		$page_uri = KintassaUtils::admin_path("KGalleryMenu", "mainpage", $page_args);
		return $page_uri;
	}

	function render_page_nav() {
		$num_records = $this->num_results();
		$pages = $this->num_pages();

		if ($pages == 1) return; // no nav if only one page

		$current_page = $this->current_page();

		echo("<div class=\"kintassa-page-nav\">{$num_records} entries found; {$pages} pages, {$this->page_size} entries per page. Go to page: ");

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
		$qry = "SELECT COUNT(*) FROM `{$this->table_name}` WHERE `gallery_id`={$this->gallery_id}";
		return $qry;
	}

	function build_page_query() {
		$page_size = $this->page_size();

		$page_num = $this->current_page();
		$page_num -= 1; // count from zero

		$gallery_id = $this->gallery_id;

		$start_item = $page_size * $page_num;
		$qry = "SELECT id,sort_pri,filepath,name,description FROM `{$this->table_name}` WHERE `gallery_id`={$gallery_id} ORDER BY `sort_pri` ASC, `name` ASC LIMIT {$start_item},{$page_size}";

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

?>