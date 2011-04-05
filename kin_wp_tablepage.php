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

abstract class KintassaTableRowForm extends KintassaForm {
	function set_row($id) {
		$this->remove_child_by_name("row_id");

		$row_id_field = new KintassaHiddenField("row_id");
		$row_id_field->set_value($id);
		$this->add_child($row_id_field);
	}

	function submitted_row_id() {
		// TODO: not implemented
		return 1;
	}

	function submitted_action() {
		// TODO: not implemented
		return 'edit';
	}

	abstract function handle_submitted_form();
}

abstract class KintassaTableRowOptions extends KintassaTableRowForm {
	const RowDel = 1;
	const RowEdit = 2;
	const RowSort = 4;
	const All = 7;

	function KintassaTableRowForm($form_name, $flags = KintassaTableRowForm::All) {
		$this->flags = $flags;
		$this->form_name = $form_name;

		if ($flags & KintassaTableRowForm::RowSort) {
			$this->add_child(new KintassaButton("Up"));
			$this->add_child(new KintassaButton("Down"));
		}
		if ($flags & KintassaTableRowForm::RowEdit) {
			$this->add_child(new KintassaButton("Edit"));
		}
		if ($flags & KintassaTableRowForm::RowDel) {
			$this->add_child(new KintassaButton("Del"));
		}
	}

	function handle_submitted_form() {
		$id = $this->submitted_row_id();
		$action = $this->submitted_action();

		echo "Action: {$action}";

		$allowed_actions = array(
			'up'		=> KintassaTableRowForm::RowSort,
			'down'		=> KintassaTableRowForm::RowSort,
			'edit'		=> KintassaTableRowForm::RowEdit,
			'delete'	=> KintassaTableRowForm::RowDel
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

	abstract function do_up($id);
	abstract function do_down($id);
	abstract function do_edit($id);
	abstract function do_del($id);
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