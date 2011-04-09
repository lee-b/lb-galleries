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
		$this->validation_error("test message");
	}

	function validation_error($err_msg) {
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
		return array("kintassa_form_el");
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

	function render() {
		$this->render_validation_errors();
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

	function child_field_names($recurse = True) {
		$child_names = array();
		foreach ($this->children as $ch) {
			if (is_a($ch, 'KintassaField')) {
				$child_names[] = $ch->name();
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
	function render() {
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

	function is_valid() {
		foreach ($this->children as $ch) {
			if (!$ch->is_valid()) {
				return false;
			}
		}
		return true;
	}

	function begin_container() {}
	function end_container() {}
}

/***
 * Provides a horizontal band to layout/divide form elements
 */
class KintassaFieldBand extends KintassaFieldContainer {
	function begin_container() {
		parent::begin_container();
		$name = $this->name();
		echo("<div class=\"kintassa_field,kintassa_field_band\" id=\"{$name}\">");
	}

	function end_container() {
		echo("</div>");
	}
}

class KintassaTextField extends KintassaEditableField {
	function render() {
		$name = $this->name();
		$def_val = $this->default_value();

		$val = $this->value();
		if ($val == null) {
			$val = $def_val;
		}

		$cl = $this->class_attrib_str();

		echo("<span {$cl}>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"{$val}\">");
		echo("</span>");
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
		return $this->default_value;
	}

	function render() {
		$val = $this->value();
		echo("<input type=\"hidden\" name=\"{$this->name}\" value=\"{$val}\"");
	}
}

class KintassaButton extends KintassaField {
	function __construct($label, $name=null, $primary=false) {
		parent::__construct($label, $name=$name);
		$this->primary = $primary;
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

		echo("<input type=\"submit\" {$cl} id=\"{$name}\" name=\"{$name}\" value=\"{$this->label}\">");
	}
}

class KintassaNumberField extends KintassaTextField {
	function render() {
		$name = $this->name();
		$cl = $this->classes();
		echo("<span {$cl}>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
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
	function begin_container() {
		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<div name=\"{$name}\" id=\"{$name}\" {$cl}");
	}

	function end_container() {
		echo("</div>");
	}

	function is_valid() {
		if (!isset($_POST[$this->name()])) {
			$this->validation_error($this->name() . " not posted");
			return false;
		}

		$post_val = $_POST[$this->name()];
		$child_fields = $this->child_field_names();
		return in_array($post_val, $child_fields);
	}

	function value() {
		assert($this->is_valid()); // shouldn't call method if !is_valid()
		$post_val = $_POST[$this->name()];
		return $post_val;
	}
}

class KintassaRadioButton extends KintassaField {
	function render() {
		$parent = $this->parent();
		assert(is_a($parent, "KintassaRadioGroup"));

		$radio_group_name = $this->parent()->name();

		$id = $this->name();
		$label = $this->label;
		$val = $this->name;

		$cl = $this->class_attrib_str();
		echo("<span {$cl}>");
		echo("<input type=\"radio\" id=\"{$id}\" name=\"{$radio_group_name}\" value=\"{$val}\">");
		echo("<label for=\"{$id}\">{$label}</label>");
		echo("</span>");
	}

	function classes() {
		$cl = parent::classes();
		$cl[] = "KintassaRadioButton";
		return $cl;
	}
}

abstract class KintassaForm extends KintassaPageElement {
	function __construct($name) {
		assert($name == strtolower($name));

		$this->name = $name;

		$this->children = array();

		/* if running under wordpress, use its nonce facilities
		   for added security */
		if (KintassaPlatform::is_wordpress()) {
			$this->nonce = new KintassaWPNonceField();
			$this->add_child($this->nonce);
		}
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
		$cl = $this->class_attrib_str();
		echo "<form {$cl} method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\" enctype=\"multipart/form-data\">";
	}

	function end_form() {
		echo "</form>";
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
		foreach ($btns as $btn) {
			$real_btn_name = $this->field_name($btn);

			if (isset($_POST[$real_btn_name])) {
				return array($btn, $this);
			}

			foreach ($this->children as $ch) {
				if (is_a($ch, 'KintassaForm')) {
					if ($ch->buttons_submitted($btn)) {
						return array($btn, $ch);
					}
				}
			}
		}

		return null;
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
			$this->handle_submissions();
		}

		$this->render();
	}

	abstract function handle_submissions();
}

?>