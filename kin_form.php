<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_platform.php");
require_once("kin_utils.php");

abstract class KintassaPageElement {
	const max_depth = 10;

	function __construct() {
		$this->_parent = null;
		$this->validation_errors = array();
		$this->extra_classes = array();
	}

	function add_class($cls) {
		if (!in_array($cls, $this->extra_classes)) {
			$this->extra_classes[] = $cls;
		}
	}

	function validation_error($err_msg) {
		$this->add_class("form-invalid");
		$this->validation_errors[] = $err_msg;
	}

	function render_validation_errors() {
		foreach ($this->validation_errors as $err_msg) {
			echo ("<div class=\"warning\">{$err_msg}</div>");
		}
	}

	function _set_parent($p) {
		$this->_parent = $p;
	}

	function parent() {
		return $this->_parent;
	}

	function classes() {
		$cl = array_merge(array("kintassa_form_el"), $this->extra_classes);
		return $cl;
	}

	function class_attrib_str() {
		$cl = $this->classes();
		return "class=\"" . implode(" ", $cl) . "\"";
	}

	function parent_of_type($typename) {
		$count = 0;
		$p = $this->parent();

		while ($count < KintassaFormElement::max_depth) {
			if (($p != null) && is_a($p, $typename)) {
				return $p;
			}

			$p = $p->parent();
			$count += 1;
		}

		return null;
	}

	function block_layout() {
		return false;
	}

	function begin_render() {
		$cl = $this->class_attrib_str();

		$tag = $this->block_layout() ? "div" : "span";
		echo("<{$tag} {$cl}>");

		$this->render_validation_errors();
	}

	function end_render() {
		$tag = $this->block_layout() ? "div" : "span";
		echo("</{$tag}>");
	}

	function render() {
		$this->begin_render();
		$this->end_render();
	}
}

abstract class KintassaFormElement extends KintassaPageElement {
	function parent_form() {
		$parent_form = $this->parent_of_type('KintassaForm');
		assert ($parent_form != null); // form elements need to be in a form
		return $parent_form;
	}

	/***
	 * Since most fields accept whatever values are given, this
	 * defaults to True.  MUST be overridden when further checks are
	 * required.
	 */
	function is_valid() {
		return true;
	}

	/***
	 * returns raw field names from children, suitable for radiogroups
	 */
	function child_field_names($recurse = True) {
		$child_names = array();
		foreach ($this->children as $ch) {
			if (is_a($ch, 'KintassaField')) {
				$child_names[] = $ch->name;
			} else if ($recurse) {
				$tmp_names = $ch->child_field_names();
				$new_child_names = array_merge($child_names, $tmp_names);
				$child_names = $new_child_names;
			}
		}
		return $child_names;
	}
}

abstract class KintassaNamedFormElement extends KintassaFormElement {
	function __construct($label, $name = null) {
		parent::__construct();

		$this->label = $label;

		if ($name != null) {
			assert($name == strtolower($name));
			$this->name = $name;
		} else {
			$this->name = KintassaNamedFormElement::label_to_name($label);
		}
	}

	function name() {
		$parent_form = $this->parent_form();
		return $parent_form->field_name($this->name);
	}

	function is_present() {
		return isset($_POST[$this->name()]);
	}

	static function build_name($form_name, $el_name) {
		assert($form_name == strtolower($form_name));
		assert($el_name == strtolower($el_name));

		return $form_name . "_" . $el_name;
	}

	static function label_to_name($label) {
		$lower_label = strtolower($label);
		$name = str_replace(" ", "_", $lower_label);
		return $name;
	}
}

abstract class KintassaField extends KintassaNamedFormElement {
}

class KintassaWPNonceField extends KintassaField {
	function begin_render() {
		parent::begin_render();

		$action = -1;
		$name = $this->name();
		$referer = true;
		$echo = false;
		$html = wp_nonce_field($action, $name, $referer, $echo);

		echo ($html);
	}

	function is_valid() {
		$name = $this->name();

		if (wp_is_admin()) {
			return check_admin_referer($name);
		} else {
			return wp_verify_nonce($name);
		}
	}
}

abstract class KintassaEditableField extends KintassaField {
	function __construct($label, $name=null, $default_val=null) {
		parent::__construct($label, $name=$name);
		$this->default_val = $default_val;
	}

	function default_value() {
		return $this->default_val;
	}

	abstract function value();
}

abstract class KintassaFieldContainer extends KintassaNamedFormElement {
	function __construct($label, $name = null) {
		parent::__construct($label, $name = $name);
		$this->children = array();
	}

	function add_child($ch) {
		$this->children[] = $ch;
		$ch->_set_parent($this);
	}

	function begin_render() {
		parent::begin_render();

		foreach ($this->children as $ch) {
			$ch->render();
		}
	}

	function is_valid() {
		foreach ($this->children as $ch) {
			if (!$ch->is_valid()) {
				return false;
			}
		}
		return true;
	}
}

/***
 * Provides a horizontal band to layout/divide form elements
 */
class KintassaFieldBand extends KintassaFieldContainer {
	function block_layout() {
		return true;
	}
}

class KintassaTextField extends KintassaEditableField {
	function begin_render() {
		parent::begin_render();

		$name = $this->name();
		$def_val = $this->default_value();

		$val = $this->value();
		if ($val == null) {
			$val = $def_val;
		}

		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"{$val}\">");
	}

	function value() {
		$fn = $this->name();

		if (isset($_POST[$fn])) {
			$val = $_POST[$fn];
		} else {
			$val = null;
		}

		return $val;
	}
}

class KintassaHiddenField extends KintassaEditableField {
	function value() {
		$name = $this->name();

		if (isset($_POST[$name])) {
			$val = $_POST[$name];
		} else {
			$val = $this->default_val;
		}

		return $val;
	}

	function render() {
		$val = $this->value();
		$name = $this->name();
		echo("<input type=\"hidden\" name=\"{$name}\" value=\"{$val}\">");
	}
}

class KintassaButton extends KintassaField {
	function __construct($label, $name=null, $primary=false) {
		parent::__construct($label, $name=$name);
		$this->primary = $primary;
	}

	function submitted() {
		$full_name = $this->name();
		return isset($_POST[$full_name]);
	}

	function classes() {
		$cl = parent::classes();

		if ($this->primary) {
			$cl[] = "button-primary";
		} else {
			$cl[] = "button-secondary";
		}

		return $cl;
	}

	function render() {
		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<input type=\"submit\" {$cl} name=\"{$name}\" value=\"{$this->label}\">");
	}
}

class KintassaNumberField extends KintassaTextField {
	function render() {
		$name = $this->name();
		$cl = $this->classes();

		echo("<span {$cl}>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" id=\"{name}\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}

	function is_valid() {
		$name = $this->name();

		if  (!$this->is_present()) {
			$this->validation_error("Not provided");
			return false;
		}

		$form_val = $_POST[$name];
		return is_numeric($form_val);
	}
}

class KintassaIntegerField extends KintassaNumberField {
	function is_valid() {
		$name = $this->name();
		if  (!$this->is_present()) {
			$this->validation_error("Not provided");
			return false;
		}
		$val = $_POST[$name];
		$is_int = KintassaUtils::isInteger($val);
		if (!$is_int) {
			$this->validation_error("Not an integer");
		}
		return $is_int;
	}
}

class KintassaCheckbox extends KintassaField {
	function render() {
		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<span {$cl}>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}
}

class KintassaFileField extends KintassaField {
	function render() {
		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<span {$cl}>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"file\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}

	function is_valid() {
		$ok = ($_FILE[$name]['error'] == UPLOAD_ERR_OK);
		if (!$ok) {
			$this->validation_error("Upload failed; please retry");
		}
		return $ok;
	}
}

class KintassaRadioGroup extends KintassaFieldContainer {
	function block_layout() {
		return true;
	}

	function is_valid() {
		if (!isset($_POST[$this->name()])) {
			$this->validation_error($this->name() . " not posted");
			return false;
		}

		$post_val = $_POST[$this->name()];
		$child_fields = $this->child_field_names();
		$present = in_array($post_val, $child_fields);

		if (!$present) {
			$allowed_opts = implode(",", $child_fields);
			$this->validation_error($post_val . " isn't a recognised option for this radio group [allowed: " . $allowed_opts . "]");
		}

		return $present;
	}

	function value() {
		assert($this->is_valid()); // shouldn't call method if !is_valid()
		$post_val = $_POST[$this->name()];
		return $post_val;
	}
}

class KintassaRadioButton extends KintassaField {
	function begin_render() {
		parent::begin_render();

		$parent = $this->parent();
		assert(is_a($parent, "KintassaRadioGroup"));

		$radio_group_name = $this->parent()->name();

		$id = $this->name();
		$label = $this->label;
		$val = $this->name;

		$cl = $this->class_attrib_str();
		echo("<input type=\"radio\" id=\"{$id}\" name=\"{$radio_group_name}\" value=\"{$val}\">");
		echo("<label for=\"{$id}\">{$label}</label>");
	}

	function block_layout() {
		return true;
	}

	function classes() {
		$cl = parent::classes();
		$cl[] = "KintassaRadioButton";
		return $cl;
	}
}

abstract class KintassaForm extends KintassaFieldContainer {
	function __construct($label, $name = null) {
		parent::__construct($label, $name);

		assert(strlen($this->name) > 0);

		$this->children = array();

		/* if running under wordpress, use its nonce facilities
		   for added security */
		if (KintassaPlatform::is_wordpress()) {
			$this->nonce = new KintassaWPNonceField();
			$this->add_child($this->nonce);
		}
	}

	function add_child($ch) {
		$this->children[] = $ch;
		$ch->_set_parent($this);
	}

	function begin_render() {
		if ($this->_parent) {
			parent::begin_render();
		} else {
			$form_uri = $this->uri();
			$form_name = $this->name();
			$cl = $this->class_attrib_str();

			echo "<form {$cl} method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\" enctype=\"multipart/form-data\">";
		}

		foreach ($this->children as $ch) {
			$ch->render();
		}
	}

	function end_render() {
		if ($this->_parent) {
			parent::end_render();
		} else {
			echo "</form>";
		}
	}

	function name() {
		return $this->name;
	}

	function field_name($fieldname) {
		assert($fieldname == strtolower($fieldname));
		return KintassaNamedFormElement::build_name($this->name(), $fieldname);
	}

	/***
	 * checks every button on the form to see if it was submitted, and matches
	 * a given button name.  Also checks subforms by calling their own
	 * button_submitted method in sequence.
	*/
	function buttons_submitted($btns) {
		foreach ($this->children as $ch) {
			if (!is_a($ch, 'KintassaButton')) continue; // only buttons
			if (!in_array($ch->name, $btns)) continue; // only allowed actions
			if ($ch->submitted()) {
				return array($ch, $this);
			}
		}

		return null;
	}

	function uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	function is_valid() {
		foreach ($this->children as $ch) {
			if (!$ch->is_valid()) {
				return false;
			}
		}
		return true;
	}

	function execute() {
		if ($this->is_valid()) {
			$render = !($this->handle_submissions());
		} else {
			$render = true;
		}

		if ($render) $this->render();
	}

	abstract function handle_submissions();
}

?>