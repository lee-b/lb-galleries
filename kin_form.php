<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to use this product.
*/

require_once("kin_platform.php");

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
		// field, which violates the Kintassa Form API, so we SHOULD fail.
		exit("KintassaFormElement::parent_form(): orphan element detected.");
	}

	/***
	 * Since most fields accept whatever values are given, this
	 * defaults to True.  MUST be overridden when further checks are
	 * required.
	 */
	function is_valid() {
		return true;
	}

	abstract function render();
}

abstract class KintassaNamedFormElement extends KintassaFormElement {
	function KintassaNamedFormElement($label, $name = null) {
		parent::KintassaFormElement();

		$this->label = $label;

		if ($name != null) {
			assert($name == strtolower($name));
			$this->name = $name;
		} else {
			$this->name = KintassaNamedFormElement::label_to_name($label);
		}
	}

	function name() {
		$form_name = $this->parent_form()->name();
		return KintassaNamedFormElement::build_name($form_name, $this->name);
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
	function KintassaField($label, $name=null, $default_val=null) {
		parent::KintassaField($label, $name=$name);
		$this->default_val = $default_val;
	}

	function default_value() {
		return $this->default_val;
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
		echo("<div id=\"{$name}\">");
	}

	function end_container() {
		echo("</div>");
	}
}

class KintassaTextField extends KintassaEditableField {
	function render() {
		$name = $this->name();
		echo("<div>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</div>");
	}
}

class KintassaHiddenField extends KintassaTextField {
	function render() {}
}

class KintassaButton extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<input type=\"submit\" value=\"{$this->label}\" name=\"{$name}\">");
	}
}

class KintassaNumberField extends KintassaTextField {
	function render() {
		$name = $this->name();
		echo("<span>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"text\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}

	function is_valid() {
		$name = $this->name();

		if  (!$this->is_present()) return false;

		$form_val = $_POST[$name];
		return is_numeric($form_val);
	}
}

class KintassaIntegerField extends KintassaNumberField {
	function isInteger($val) {
		return (preg_match('@^[-]?[0-9]+$@',$val) === 1);
	}

	function is_valid() {
		$name = $this->name();
		if  (!$this->is_present()) return false;
		$val = $_POST[$name];
		return $this->isInteger($val);
	}
}

class KintassaCheckbox extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<span>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"checkbox\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}
}

class KintassaFileField extends KintassaField {
	function render() {
		$name = $this->name();
		echo("<span>");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("<input type=\"file\" name=\"{$name}\" value=\"\">");
		echo("</span>");
	}

	function is_valid() {
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

		echo("<span class=\"KintassaRadioButton\">");
		echo("<input type=\"radio\" name=\"{$radio_group_name}\" value=\"{$name}\">");
		echo("<label for=\"{$name}\">{$this->label}</label>");
		echo("</span>");
	}
}

abstract class KintassaForm {
	function KintassaForm($name) {
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
		echo "<form method=\"post\" action=\"{$form_uri}\" name=\"{$form_name}\" enctype=\"multipart/form-data\">";
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