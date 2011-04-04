<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaFormElement {
	const max_depth = 10;

	function KintassaFormElement() {
		$this->_parent = null;
	}

	function _set_parent($p) {
		$this->_parent = $p;
	}

	function parent() {
		return $this->_parent;
	}

	function parent_form() {
		$count = 0;
		$p = $this->parent();

		while ($count < KintassaFormElement::max_depth) {
			if (($p != null) && is_a($p, 'KintassaForm')) {
				return $p;
			}

			$p = $p->parent();
			$count += 1;
		}

		// if we get here, there is no parent defined for the form
		// field, so we SHOULD fail.
		$p = null;
		assert ($p == null);
	}

	/***
	 * Since most fields accept whatever values are given, this
	 * defaults to True.  MUST be overridden when further checks are
	 * required.
	 */
	function is_valid($post_vars, $file_vars) {
		return true;
	}

	abstract function render();
}

abstract class KintassaNamedFormElement extends KintassaFormElement {
	function KintassaNamedFormElement($label, $name = null) {
		parent::KintassaFormElement();

		$this->label = $label;

		if ($name != null) {
			$this->name = $this->name;
		} else {
			$this->name = KintassaNamedFormElement::label_to_name($label);
		}
	}

	function name() {
		$form_name = $this->parent_form()->name();
		return KintassaNamedFormElement::build_name($form_name, $this->name);
	}

	static function build_name($form_name, $el_name) {
		return $form_name . "_" . $el_name;
	}

	static function label_to_name($label) {
		$name = strtolower($label);
		$name = str_replace(" ", "_", $name);
		return $name;
	}
}

abstract class KintassaField extends KintassaNamedFormElement {
}

class KintassaWPNonceField extends KintassaField {
	function render() {
		$action = -1;
		$name = $this->name();
		$referer = true;
		$echo = false;
		$html = wp_nonce_field($action, $name, $referer, $echo);

		echo ($html);
	}

	function is_valid($post_vars, $file_vars) {
		$name = $this->name();

		if (wp_is_admin()) {
			return check_admin_referer($name);
		} else {
			return wp_verify_nonce($name);
		}
	}
}

abstract class KintassaFieldContainer extends KintassaNamedFormElement {
	function KintassaFieldContainer($label, $name = null) {
		parent::KintassaNamedFormElement($label, $name = $name);
		$this->children = array();
	}

	function add_child($ch) {
		$this->children[] = $ch;
		$ch->_set_parent($this);
	}

	function render() {
		$this->begin_container();
		$this->render_children();
		$this->end_container();
	}

	function render_children() {
		foreach ($this->children as $ch) {
			$ch->render();
		}
	}

	function is_valid($post_vars, $file_vars) {
		foreach ($this->children as $ch) {
			if (!$ch->is_valid($post_vars, $file_vars)) {
				return false;
			}
		}
		return true;
	}

	function begin_container() {
		$name = $this->name();
		echo("<div id=\"{$name}\">");
	}

	function end_container() {
		echo("</div>");
	}
}

class KintassaTextField extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}
}

class KintassaButton extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<input type=\"submit\" value=\"{$this->label}\" name=\"{$name}\">");
		echo("</div>");
	}
}

class KintassaNumberField extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function is_valid($post_vars, $file_vars) {
		$name = $this->name();
		$val = $post_vars[$name];
		return is_numeric($val);
	}
}

class KintassaIntegerField extends KintassaNumberField {
	function isInteger($val) {
		return (preg_match('@^[-]?[0-9]+$@',$val) === 1);
	}

	function is_valid($post_vars, $file_vars) {
		$name = $this->name();
		$val = $post_vars[$name];
		return $this->isInteger($val);
	}
}

class KintassaCheckbox extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}
}

class KintassaFileField extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"file\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function is_valid($post_vars, $file_vars) {
		return ($_FILE[$name]['error'] == UPLOAD_ERR_OK);
	}
}

class KintassaRadioGroup extends KintassaFieldContainer {
	function begin_container() {
		$name = $this->name();
		echo("<div name=\"{$name}\" id=\"{$name}\" class=\"\">");
	}

	function end_container() {
		echo("</div>");
	}
}

class KintassaRadioButton extends KintassaField {
	function render() {
		$parent = $this->parent();
		assert(is_a($parent, "KintassaRadioGroup"));

		$radio_group_name = $this->parent()->name();
		$name = $this->name();

		echo("<div class=\"KintassaRadioButton\">");
		echo("<input type=\"radio\" name=\"{$radio_group_name}\" value=\"{$name}\">");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("</div>");
	}
}

abstract class KintassaForm {
	function KintassaForm($name) {
		$this->name = $name;
		$this->children = array();
	}

	function parent() {
		// TODO: need logic here if supporting subforms
		return null;
	}

	function add_child($ch) {
		$this->children[] = $ch;
		$ch->_set_parent($this);
	}

	function begin_form() {
		$form_uri = $this->uri();
		$form_name = $this->name();
		echo "<form method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\" enctype=\"multipart/form-data\">";
	}

	function end_form() {
		echo "</form>";
	}

	function name() {
		return "kin_frm_{$this->name}";
	}

	function field_name($fieldname) {
		return KintassaNamedFormElement::build_name($this->name(), $fieldname);
	}

	/***
	 * shortcut to test if a certain button has been submitted, without
	 * building the entire form.
	*/
	function have_submission($btn) {
		$real_btn_name = $this->field_name($btn);
		$set = isset($_POST[$real_btn_name]);
		return $set;
	}

	function uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	function render() {
		$this->begin_form();
		foreach ($this->children as $ch) {
			$ch->render();
		}
		$this->end_form();
	}
}

/***
 * Represents a very simple, complete-and-submit style single-option form,
 * where the only submit button is simply called 'submit'.
 */
abstract class KintassaWPForm extends KintassaForm {
}

?>