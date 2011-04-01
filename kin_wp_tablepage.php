<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_micro_orm.php");
require_once("kin_wp_form.php");
require_once("kin_wp_page.php");

abstract class KintassaWPTableRowOptions {
	const RowAdd = 1;
	const RowDel = 2;
	const RowEdit = 4;
	const RowSort = 8;
	const All = 15;

	function KintassaWPTableRowOptions($form_name, $flags = KintassaWPTableRowOptions::All) {
		$this->flags = $flags;
		$this->form_name = $form_name;
	}

	function form_id() {
		return $this->form_name . "_wp_tablerowopts";
	}

	/***
	 * returns true if a matching form has been submitted.  Applies to all row instances,
	 * allowing to act on commands before rendering each row.
	 */
	function have_submission() {
		$form_id = $this->form_id();
		return (isset($_POST['kintassa_form_id']) && $_POST['kintassa_form_id'] == $form_id);
	}

	function begin_form($id) {
		// TODO: proper form action uri
		$form_id = $this->form_id();
		$this_uri = esc_url($_SERVER['REQUEST_URI']);
		echo "<form method=\"POST\" name=\"{$form_id}\" action=\"{$this_uri}\">";
		echo "<input type=\"hidden\" name=\"kintassa_form_id\" value=\"${form_id}\">";
	}

	function end_form() {
		echo "</form>";
	}

	function render_button($label, $name=null) {
		if ($name == null) {
			$name = str_replace(' ', '_', strtolower($label));
		}
		echo "<input type=\"submit\" name=\"{$name}\" value=\"${label}\">";
	}

	function render_form($row) {
		$id = $row->id;
		$this->begin_form($id);

		if ($this->flags & KintassaWPTableRowOptions::RowSort) {
			$this->render_button("up");
		}
		if ($this->flags & KintassaWPTableRowOptions::RowSort) {
			$this->render_button("down");
		}
		if ($this->flags & KintassaWPTableRowOptions::RowEdit) {
			$this->render_button("edit");
		}
		if ($this->flags & KintassaWPTableRowOptions::RowDel) {
			$this->render_button("del");
		}

		$this->end_form();
	}

	function submitted_row_id() {
		// TODO: not implemented
		return 1;
	}

	function submitted_action() {
		// TODO: not implemented
		return 'edit';
	}

	abstract function do_up($id);
	abstract function do_down($id);
	abstract function do_edit($id);
	abstract function do_del($id);

	function handle_submitted_form() {
		$id = $this->submitted_row_id();
		$action = $this->submitted_action();

		echo "Action: {$action}";

		$allowed_actions = array(
			'up'		=> KintassaWPTableRowOptions::RowSort,
			'down'		=> KintassaWPTableRowOptions::RowSort,
			'edit'		=> KintassaWPTableRowOptions::RowEdit,
			'delete'	=> KintassaWPTableRowOptions::RowDel
		);

		if (array_key_exists($action, $allowed_actions)) {
			$required_opt = $allowed_actions[$action];

			// only act if this form is specified to enable
			// the given action
			if ($this->flags & $required_opt) {
				// yes, proceed
				$funcname = 'do_' . $action;
				$func = $this->$funcname($id);
			}
		}
	}

	function execute($row) {
		// look for a submitted row userdata field, and dispatch to the appropriate
		// handler function based on submitted form button value
		if ($this->have_submission()) {
			$this->handle_submitted_form();
		}

		$this->render_form($row);
	}
}

abstract class KintassaWPTablePage extends KintassaWPPage {
	function KintassaWPTablePage($kORMClass, $form_opts, $serial = null, $fields = null, $filter = null) {
		parent::KintassaWPPage();

		$this->kORMClass = $kORMClass;
		$this->fields = $fields;
		$this->filter = $filter;

		$this->form_opts = $form_opts;
	}

	function render_row($r) {
		// TODO: render correct fields
		echo "<tr>";

		foreach ($r as $f) {
			echo "<td>{$f}</td>";
		}

		echo "<td>";
			$this->form_opts->execute($r);
		echo "</td>";

		echo "</tr>";
	}

	function render_no_records() {
		// TODO: render correct colspan
		echo <<<HTML
	<tr>
		<th colspan="4">(No records found)</th>
	</tr>
HTML;
	}

	function render_header() {
		// TODO: render correct headings
		echo <<<HTML
<table class="widefat">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Width</th>
			<th>Height</th>
			<th>Options</th>
			</tr>
	</thead>
	<tfoot>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Width</th>
			<th>Height</th>
			<th>Options</th>
		</tr>
	</tfoot>
	<tbody>
HTML;
	}

	function render_footer() {
		echo <<<HTML
	</tbody>
</table>
HTML;

		// TODO: footer buttons
//		$this->render_button("AddNew", "Add New");
	}

	function execute() {
		$this->render_header();

		$cls = $this->kORMClass;
		$rows = KintassaMicroORMObject::get_rows($cls::table_name());

		if ($rows) {
			foreach ($rows as $r) {
				$this->render_row($r);
			}
		} else {
			$this->render_no_records();
		}

		$this->render_footer();
	}
}

?>