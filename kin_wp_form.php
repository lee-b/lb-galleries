<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

abstract class KintassaFormElement {
	const max_depth = 10;

	function KintassaField() {
		$this->parent = null;
	}

	function _set_parent($p) {
		$this->parent = $p;
	}

	function parent_form() {
		$count = 0;
		$p = $this->parent;

		while ($count < $max_depth) {
			if (($p != null) && is_a($p, KintassaForm)) {
				return $p;
			}
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
		$this->label = $label;

		if ($name != null) {
			$this->name = $this->name;
		} else {
			$label_as_name = KintassaNamedFormElement::label_to_name($label);
		}
	}

	function name() {
		$form_name = $this->form()->name();
		return KintassaNamedFormElement::build_element_name($form_name, $this->name);
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
	function KintassaFieldContainer() {
		$this->children = array();
	}

	function add_child($ch) {
		$this->children.add($ch);
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

	abstract function begin_container();
	abstract function end_container();
}

class KintasssaTextField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}
}

class KintassaButton {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<input type=\"submit\" value=\"{$label}\" name=\"{$btn_name}\">");
		echo("</div>");
	}
}

class KintassaNumberfield {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function is_valid($post_vars, $file_vars) {
		$name = $this->
		$val = $post_vars[$name];
		return is_numeric($val);
	}
}

class KintassaIntegerField {
}

class KintassaCheckbox {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}
}

class KintassaRadioButton extends KintassaField {
	function render() {
		$name = $this->name();
		// TODO: not implemented
		assert (null);
	}
}

class KintassaFileField extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$label}</label>");
		echo("<input type=\"file\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}

	function is_valid($post_vars, $file_vars) {
		return ($_FILE[$name]['error'] == UPLOAD_ERR_OK);
	}
}

class KintassaRadioGroup extends KintassaFieldContainer {
	function begin_container() {
		// TODO: not implemented
		assert(null);
	}

	function end_container() {
		// TODO: not implemented
		assert (null);
	}
}

abstract class KintassaForm {
	function KintassaForm($name) {
		$this->name = $name;
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
		// TODO: check this matches the code in KintassaNamedFormElement.
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
