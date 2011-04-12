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
	const static_prefix = "kintassa_";

	function __construct() {
		$this->_parent = null;
		$this->validation_errors = array();
		$this->extra_classes = array();
		$this->user_messages = array();
	}

	function add_class($cls) {
		if (!in_array($cls, $this->extra_classes)) {
			$this->extra_classes[] = $cls;
		}
	}

	function user_message($msg) {
		$this->user_messages[] = $msg;
	}

	function render_user_messages() {
		foreach ($this->user_messages as $msg) {
			echo("<div class=\"user-message\">{$msg}</div>");
		}
	}

	function validation_error($err_msg) {
		// shouldn't be posting errors if the form hasn't been submitted yet
		assert($this->is_present());

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

	function begin_render($as_sub_el = false) {
		$cl = $this->class_attrib_str();

		$tag = $this->block_layout() ? "div" : "span";
		echo("<{$tag} {$cl}>");

		$this->render_validation_errors();
		$this->render_user_messages();
	}

	function end_render($as_sub_el = false) {
		$tag = $this->block_layout() ? "div" : "span";
		echo("</{$tag}>");
	}

	function render($as_sub_el = false) {
		$this->begin_render($as_sub_el);
		$this->end_render($as_sub_el);
	}
}

abstract class KintassaFormElement extends KintassaPageElement {
	function parent_form() {
		$parent_form = $this->parent_of_type('KintassaForm');
		assert ($parent_form != null); // form elements need to be in a form
		return $parent_form;
	}

	function is_present() {
		return false;
	}

	function validate_posted_data() {
		return true;
	}

	/***
	 * Since most fields accept whatever values are given, this
	 * defaults to True.  MUST be overridden when further checks are
	 * required.
	 */
	function is_valid() {
		if (!$this->is_present()) {
			return true;
		}
		return $this->validate_posted_data();
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
		$pres = isset($_POST[$this->name()]);
		return $pres;
	}

	function posted_value() {
		$field_name = $this->name();
		if (!$this->is_present()) return null;
		$posted_val = $_POST[$field_name];
		return $posted_val;
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

class KintassaLinkButton extends KintassaNamedFormElement {
	function __construct($label, $name=null, $uri = "/", $args = array()) {
		parent::__construct($label, $name = $name);
		$this->uri = $uri;
		$this->args = $args;
	}

	function classes() {
		$cl = parent::classes();
		$cl[] = "button";
		return $cl;
	}

	function begin_render($as_sub_el = false) {
//		parent::begin_render($as_sub_el);
		$cl = $this->class_attrib_str();
		$uri = $this->uri;
		$label = $this->label;
		$args = implode("&", $this->args);
		echo("<a {$cl} href=\"{$uri}\">{$label}</a>");
	}
}

abstract class KintassaField extends KintassaNamedFormElement {
}

class KintassaWPNonceField extends KintassaField {
	function begin_render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		$action = -1;
		$name = $this->name();
		$referer = true;
		$echo = false;
		$html = wp_nonce_field($action, $name, $referer, $echo);

		echo ($html);
	}

	function validate_posted_data() {
		// NOTE: no parent call here, as wordpress does the whole job for us
		$name = $this->name();

		if (wp_is_admin()) {
			$res = check_admin_referer($name);
		} else {
			$res = wp_verify_nonce($name);
		}

		if (!$res) {
			$form = $this->parent_form();
			$form->validation_error(__("Form validation failed."));
		}

		return $res;
	}
}

abstract class KintassaEditableField extends KintassaField {
	function __construct($label, $name=null, $default_val=null, $required = true) {
		parent::__construct($label, $name=$name);
		$this->default_val = $default_val;
		$this->required = $required;
	}

	function default_value() {
		return $this->default_val;
	}

	function validate_posted_data() {
		if (!parent::validate_posted_data()) return false;

		if ($this->required) {
			if ($this->posted_value() == null) {
				$this->validation_error(__("This field is required"));
				return false;
			}
		}

		return true;
	}

	function value() {
		$posted_val = $this->posted_value();
		if (!$posted_val) return $this->default_value();

		return $posted_val;
	}
}

abstract class KintassaFieldContainer extends KintassaNamedFormElement {
	function __construct($label, $name = null) {
		parent::__construct($label, $name = $name);
		$this->children = array();
	}

	function is_present() {
		foreach($this->children as $ch) {
			if (!$ch->is_present()) {
				return false;
			}
		}
		return true;
	}

	function add_child($ch) {
		$this->children[] = $ch;
		$ch->_set_parent($this);
	}

	function begin_render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		foreach ($this->children as $ch) {
			$ch->render(true);
		}
	}

	function validate_posted_data() {
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
	function posted_value() {
		$val = parent::posted_value();
		if ($val == null) return $val;

		// added filtering of parent's return value
		return esc_attr($val);
	}

	function begin_render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		$name = $this->name();
		$val = $this->value();

		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"{$val}\">");
	}
}

class KintassaHiddenField extends KintassaEditableField {
	function __construct($label, $name=null, $default_val = null, $non_unique = false) {
		assert($default_val != null);

		parent::__construct($label, $name=$name, $default_val = $default_val, $required = true);
		$this->non_unique = $non_unique;
	}

	function is_present() {
		return true;
	}

	function is_valid() {
		$val = $this->value();
		return KintassaUtils::isInteger($val);
	}

	function value() {
		return $this->default_val;
	}

	function name() {
		// TODO: code duplicated in KintassaButton.  Refactor.
		if ($this->non_unique) {
			return KintassaPageElement::static_prefix . $this->name;
		} else {
			return parent::name();
		}
	}

	function render($as_sub_el = false) {
		/*
		 * we override the standard rendering for this field, since it should
		 * be entirely hidden
		 */
		$val = $this->value();
		$name = $this->name();
		echo("<input type=\"hidden\" name=\"{$name}\" value=\"{$val}\">");
	}
}

class KintassaButton extends KintassaField {
	function __construct($label, $name=null, $primary=false, $non_unique = false) {
		parent::__construct($label, $name=$name);
		$this->primary = $primary;
		$this->non_unique = $non_unique;
	}

	function submitted() {
		$full_name = $this->name();
		return isset($_POST[$full_name]);
	}

	function name() {
		// TODO: code duplicated in KintassaHiddenField.  Refactor.
		if ($this->non_unique) {
			return KintassaPageElement::static_prefix . $this->name;
		} else {
			return parent::name();
		}
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

	function begin_render($as_sub_el = false) {
//		parent::begin_render($as_sub_el); // TODO: refactor so OK to call

		$name = $this->name();
		$cl = $this->class_attrib_str();

		echo("<input type=\"submit\" {$cl} name=\"{$name}\" value=\"{$this->label}\">");
	}

	function end_render($as_sub_el = false) {
//		parent::end_render($as_sub_el); // TODO: refactor so OK to call
	}
}

class KintassaNumberField extends KintassaTextField {
	function validate_posted_data() {
		if (!parent::validate_posted_data()) return false;

		$val = $this->posted_value();
		$is_num = is_numeric($val);

		if (!$is_num) {
			$this->validation_error(__("Please enter a number"));
		}

		return $is_num;
	}
}

class KintassaIntegerField extends KintassaNumberField {
	function validate_posted_data() {
		if (!parent::validate_posted_data()) return false;

		$val = $this->posted_value();

		$is_int = KintassaUtils::isInteger($val);
		if (!$is_int) {
			$this->validation_error(__("Please enter an integer"));
		}

		return $is_int;
	}
}

class KintassaCheckbox extends KintassaField {
	function render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
	}
}

class KintassaFileField extends KintassaField {
	function render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		$name = $this->name();
		$cl = $this->class_attrib_str();
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"file\" name=\"{$name}\" value=\"\">");
	}

	function is_valid() {
		if (!isset($_FILE[$this->name])) return false;

		$ok = ($_FILE[$this->name]['error'] == UPLOAD_ERR_OK);
		if (!$ok) {
			$this->validation_error(__("Upload failed; please retry"));
		}

		return $ok;
	}
}

class KintassaImageUploadField extends KintassaFileField {
	function render($as_sub_el = false) {
		echo "(Image upload field here )";
	}
}

class KintassaRadioGroup extends KintassaFieldContainer {
	function __construct($label, $name = null, $default_value = null) {
		parent::__construct($label, $name=$name);
		$this->default_value = $default_value;
	}

	function default_value() {
		return $this->default_value;
	}

	function block_layout() {
		return true;
	}

	function value() {
		$posted_val = $this->posted_value();
		if ($this->validate_posted_data()) {
			return $posted_val;
		} else {
			return $this->default_value();
		}
	}

	function validate_posted_data() {
		$posted_val = $this->posted_value();
		$child_fields = $this->child_field_names();

		$present = in_array($posted_val, $child_fields);
		if (!$present) {
			$this->validation_error(__("Unrecognised option chosen"));
		}

		return $present;
	}

	function is_present() {
		$res = KintassaNamedFormElement::is_present();
		return $res;
	}
}

class KintassaRadioButton extends KintassaField {
	function group_name() {
		return $this->parent()->name();
	}

	function group_present() {
		// returns true if a member of the group has been posted
		return isset($_POST[$this->group_name()]);
	}

	function group_value() {
		// returns true if a member of the group has been posted
		return $_POST[$this->group_name()];
	}

	function group_default() {
		return $this->parent()->default_value();
	}

	function selected() {
		if (!$this->group_present()) {
			// nothing posted; use default
			$val = $this->group_default();
		} else {
			$val = $this->group_value();
		}
		return ($val == $this->name);
	}

	function begin_render($as_sub_el = false) {
		parent::begin_render($as_sub_el);

		$parent = $this->parent();
		assert(is_a($parent, "KintassaRadioGroup"));

		$radio_group_name = $this->parent()->name();

		$id = $this->name();
		$label = $this->label;
		$val = $this->name;

		$cl = $this->class_attrib_str();

		$selected = "";
		if ($this->selected() == $val) $selected = " checked";

		echo("<input type=\"radio\" id=\"{$id}\" name=\"{$radio_group_name}\" value=\"{$val}\"$selected>");
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

	function begin_render($as_sub_el=false) {
//		parent::begin_render($as_sub_el); // TODO: refactor so OK to call

		if (!$as_sub_el) {
			$form_uri = $this->uri();
			$form_name = $this->name();
			$cl = $this->class_attrib_str();

			echo "<form {$cl} method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\" enctype=\"multipart/form-data\">";
		}

		foreach ($this->children as $ch) {
			$ch->render(true);
		}
	}

	function end_render($as_sub_el = false) {
		if (!$as_sub_el) {
			echo "</form>";
		}

//		parent::end_render($as_sub_el); // TODO: refactor so OK to call
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
	function buttons_submitted($btns, $children = null) {
		if ($children == null) $children = $this->children;

		foreach ($children as $ch) {
			if (is_a($ch, 'KintassaButton')) {
				if ($ch->submitted()) {
					return array($ch, $this);
				}
			} else if (is_a($ch, 'KintassaFieldContainer')) {
				$child_res = $this->buttons_submitted($btns, $ch->children);
				if ($child_res != null) return $child_res;
			}
		}

		return null;
	}

	function uri() {
		return esc_url($_SERVER['REQUEST_URI']);
	}

	function is_present() {
		return true;
	}

	function validate_posted_data() {
		foreach ($this->children as $ch) {
			$valid = $ch->is_valid();
			if (!$valid) {
				return false;
			}
		}
		return true;
	}

	function execute($as_sub_el = false) {
		if ($this->is_valid()) {
			$render = !($this->handle_submissions());
		} else {
			$render = true;
		}

		if ($render) $this->render($as_sub_el);
	}

	abstract function handle_submissions();
}

?>