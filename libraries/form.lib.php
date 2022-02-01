<?php

/**
 * Form Class
 *
 * This class primarily displays form controls
 *
 */

class form
{
	// Added the capability to call constructor without a fields
	// argument. We add a set() routine later to compensate.
	
	function __construct($fields = [])
	{
		$this->fields = $fields;
	}

	function set($fields = [])
	{
		$this->fields = $fields;
	}

	function clear()
	{
		$this->fields = [];
	}

	// =======================================================
	// Translation Routines
	// =======================================================

	/**
	 * request()
	 *
	 * Returns an array of values based on the $this->fields array. It
	 * takes the values from the POST array and fills the result array
	 * in with POST values. Items which don't show up (like unchecked
	 * check boxes) are returned as NULLs. Submit fields from POST are
	 * ignored. In the future, this code could be expanded to include
	 * field level validation and sanitization.
	 *
	 * @param array $post The POST array, or nothing
	 *
	 * @return array
	 *
	 */

	function request($post = NULL)
	{
		$result = array();

		foreach ($this->fields as $field) {
			$fieldname = $field['name'];
			if ($field['type'] != 'submit') {
				if (isset($post[$fieldname])) {
					$result[$fieldname] = $post[$fieldname];
				}
				else {
					$result[$fieldname] = NULL;
				}
			}
		}

		return $result;
	}
	 
	/**
	 * mismatch()
	 *
	 * Provides a FATAL error if the programmer specifies a field as one
	 * type, but feeds it to a different form member.
	 *
	 * This function either fails catastrophically, or does nothing.
	 *
	 * @param string $field_name The name of the field to test
	 * @param string $function The form member function called
	 */

	private function mismatch($field_name, $function)
	{
		if ($this->fields[$field_name]['type'] != $function) {
			die('<h1>Fatal field error: ' . $this->fields[$field_name]['name'] . ' called with ' . $function . ', but should be ' . $this->fields[$field_name]['type'] . '.</h1>');
		}
	}


	// =======================================================
	// Display Routines
	// =======================================================

	function key_value($key, $value)
	{
		$str = $key . '="' . $value . '" ';
		return $str;
	}

	function text($field_name, $content = NULL)
	{
		$this->mismatch($field_name, 'text');

		$fld = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'text');
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		if (!is_null($content)) {
			$str .= $this->key_value('value', $content);
		}
		elseif (isset($fld['value'])) {
			$str .= $this->key_value('value', $fld['value']);
		}

		if (isset($fld['size'])) {
			$str .= $this->key_value('size', $fld['size']);
		}

		if (isset($fld['maxlength'])) {
			$str .= $this->key_value('maxlength', $fld['maxlength']);
		}

		if (isset($fld['javascript'])) {
			$str .= ' ' . $fld['javascript'];
		}

		// added for HTML5
		if (isset($fld['required'])) {
			$str .= 'required';
		}

		$str .= '/>' . PHP_EOL;

		echo $str;
	}

	function password($field_name, $content = NULL)
	{
		$this->mismatch($field_name, 'password');

		$fld = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'password');
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);

		if (isset($fld['size'])) {
			$str .= $this->key_value('size', $fld['size']);
		}

		if (isset($fld['maxlength'])) {
			$str .= $this->key_value('maxlength', $fld['maxlength']);
		}

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		// added for HTML5
		if (isset($fld['required'])) {
			$str .= 'required';
		}

		$str .= '/>' . PHP_EOL;

		echo $str;
	}

	function date($field_name, $content = NULL)
	{
		$this->mismatch($field_name, 'date');

		$fld = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'date');
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		if (!is_null($content)) {
			$str .= $this->key_value('value', $content);
		}
		elseif (isset($fld['value'])) {
			$str .= $this->key_value('value', $fld['value']);
		}

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		// added for HTML5
		if (isset($fld['required'])) {
			$str .= 'required';
		}

		$str .= '/>' . PHP_EOL;

		echo $str;
	}

	function select($field_name, $select_value = NULL)
	{
		$this->mismatch($field_name, 'select');

		$fld = $this->fields[$field_name];
		if (isset($fld['multi']) && $fld['multi'] == 1) {
			$multi = TRUE;
		}
		else {
			$multi = FALSE;
		}
		$opts = $this->fields[$field_name]['options'];

		$str = '<select ';
		if ($multi) {
			$str .= $this->key_value('name', $fld['name'] . '[]');
		}
		else {
			$str .= $this->key_value('name', $fld['name']);
		}
		$str .= $this->key_value('id', $fld['name']);
		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}
		if (isset($fld['size'])) {
			$str .= $this->key_value('size', $fld['size']);
		}
		if ($multi) {
			$str .= 'multiple ';
		}

		// added for HTML5
		if (isset($fld['required'])) {
			$str .= 'required';
		}

		$str .= '>' . PHP_EOL;

		// options loop
		foreach ($opts as $option) {
			$str .= '<option ';
			$str .= $this->key_value('value', $option['val']);

			// determine any default value
			// default value at runtime takes precedence over default
			// value set on field creation
			if (!is_null($select_value)) {
				$sval = $select_value;
			}
			elseif (isset($fld['value'])) {
				$sval = $fld['value'];
			}
			else {
				$sval = NULL;
			}
			
			if (!is_null($sval)) {
				if (!is_array($sval)) {
					if ($option['val'] == $sval) {
						$str .= 'selected="selected" ';
					}
				}
				else {

					$max = count($sval);
					for ($i = 0; $i < $max; $i++) {
						if ($option['val'] == $sval[$i]) {
							$str .= 'selected="selected"';
							break;
						}
					}

				}
			}

			$str .= '>';
			$str .= $option['lbl'];
			$str .= '</option>' . PHP_EOL;
		}

		$str .= '</select>' . PHP_EOL;
		
		echo $str;
	}

	function bare_radio($fld, $option, $checked_value = NULL)
	{
		$str = '<input ';
		$str .= $this->key_value('type', 'radio');
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('value', $option['val']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		if (!is_null($checked_value)) {
			if ($option['val'] == $checked_value) {
				$str .= 'checked="checked" ';
			}
		}
		elseif (isset($fld['checked'])) {
			if ($option['val'] == $fld['checked']) {
				$str .= 'checked="checked" ';
			}
		}

		$str .= '/>' . PHP_EOL;

		return $str;
	}

	function radio($field_name, $checked_value = NULL)
	{
		$this->mismatch($field_name, 'radio');

		$fld = $this->fields[$field_name];
		$opts = $fld['options'];

		if (is_null($checked_value)) {
			if (array_key_exists('checked', $fld)) {
				$checked_value = $fld['checked'];
			}
		}

		$str = '';
		foreach ($opts as $option) {

			$radio_string = $this->bare_radio($fld, $option, $checked_value);

			// Label o
			if ($fld['direction'] === 'L') {
				$str .= $option['lbl'] . '&nbsp;';
				$str .= $radio_string;
			}
			// o Label
			elseif ($fld['direction'] === 'R') {
				$str .= $radio_string;
				$str .= '&nbsp;' . $option['lbl'];
			}
			// label o<br/>
			elseif ($fld['direction'] == 'LV') {
				$str .= $option['lbl'] . '&nbsp;';
				$str .= $radio_string;
				$str .= '<br/>' . PHP_EOL;
			}
			// o label<br/>
			elseif ($fld['direction'] == 'RV') {
				$str .= $radio_string;
				$str .= '&nbsp;' . $option['lbl'];
				$str .= '<br/>' . PHP_EOL;
			}
		}
		
		echo $str;
	}

	function checkbox($field_name, $checked_value = NULL)
	{
		$this->mismatch($field_name, 'checkbox');

		$fld = $this->fields[$field_name];

		$str = '<input type="checkbox" ';
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);
		$str .= $this->key_value('value', $fld['value']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		// passed value takes precedence over the "baked in" value
		if (!is_null($checked_value)) {
			if ($fld['value'] == $checked_value) {
				$str .= 'checked';
			}
		}
		elseif (isset($fld['checked'])) {
			if ($fld['value'] == $fld['checked']) {
				$str .= 'checked';
			}
		}

		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function file($field_name)
	{
		$this->mismatch($field_name, 'file');

		$parms = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'file');
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}
			
		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function hidden($field_name, $content = NULL)
	{
		$this->mismatch($field_name, 'hidden');

		$parms = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'hidden');
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);
		if (!is_null($content)) {
			$str .= $this->key_value('value', $content);
		}
		elseif (isset($parms['value'])) {
			$str .= $this->key_value('value', $parms['value']);
		}
		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function textarea($field_name, $content = NULL)
	{
		$this->mismatch($field_name, 'textarea');

		$parms = $this->fields[$field_name];

		$str = '<textarea ';
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);
		if (isset($parms['maxlength'])) {
			$str .= $this->key_value('maxlength', $parms['maxlength']);
		}

		if (isset($parms['class'])) {
			$str .= $this->key_value('class', $parms['class']);
		}

		if (isset($parms['cols'])) {
			$str .= $this->key_value('cols', $parms['cols']);
		}
		if (isset($parms['rows'])) {
			$str .= $this->key_value('rows', $parms['rows']);
		}
		if (isset($parms['wrap'])) {
			$str .= $this->key_value('wrap', $parms['wrap']);
		}

		// added for HTML5
		if (isset($fld['required'])) {
			$str .= 'required';
		}

		$str .= '>' . PHP_EOL;

		if (!is_null($content)) {
			$str .= $content;
		}
		elseif (isset($parms['value'])) {
			$str .= $parms['value'];
		}

		$str .= '</textarea>' . PHP_EOL;

		echo $str;
	}

	function submit($field_name)
	{
		$this->mismatch($field_name, 'submit');

		$parms = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'submit');
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);
		$str .= $this->key_value('value', $parms['value']);
		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	/**
	 * output()
	 *
	 * This is a generalized output routine, which can be used if you
	 * don't want to use $form->select(...) or whatever.
	 *
	 */

	function output($field_name, $content = NULL)
	{
		$field = $this->fields[$field_name];
		$this->{$field['type']}($field_name, $content);
	}

	/**
	 * show()
	 *
	 * A function to loop through all the fields and show them in a
	 * form, all in one step. This presents a form in a table, labels on
	 * the left, fields on the right. Labels are bold and right
	 * justified, because that's the way I like them. One line per
	 * label/field pair.
	 *
	 * This routine is also the only one which uses the option "label"
	 * attribute.
	 *
	 * This code is tweaked to my preferences for this kind of form.
	 * You're free to change it for you.
	 *
	 * @param array $values An associative array of values for the fields
	 *
	 */

	function show($values = array())
	{
		foreach ($this->fields as $name => $f) {

			if ($f['type'] == 'hidden') {
				if (isset($values[$name])) {
					$this->hidden($name, $values[$name]);
				}
				else {
					$this->hidden($name);
				}
				continue;
			}

			echo '<tr>' . PHP_EOL;
			echo "\t" . '<td align="right">' . PHP_EOL;

			if ($f['type'] != 'submit') {
				// label
				if (isset($f['label'])) {
					$label = $f['label'];
				}
				else {
					$label = ucfirst($f['name']);
				}	

				echo "\t\t<label>" . $label . '</label>' . PHP_EOL;
			}
			
			echo "\t</td>" . PHP_EOL;

			// field
			echo "\t<td>" . PHP_EOL;

			echo "\t\t";

			if (isset($values[$name])) {
				$this->{$f['type']}($name, $values[$name]);
			}
			else {
				$this->{$f['type']}($name);
			}

			echo "\t</td>" . PHP_EOL;

			echo '</tr>' . PHP_EOL;
		}
	}

	// =======================================================
	// Static Functions
	// =======================================================

	static function button($legend, $link)
	{
		$str = '<a href="' . $link . '"><button type="button">' . $legend . '</button></a>' . PHP_EOL;
		echo $str;
	}

	static function abandon($link)
	{
		$str = '<a href="' . $link . '"><button type="button">Abandon</button></a>' . PHP_EOL;
		echo $str;
	}

	// =======================================================
	// Other Routines
	// =======================================================

	/**
	 * check_files()
	 *
	 * This does a job similar to check_requireds() except that it does
	 * so against the FILES array (passed in)
	 *
	 * @parm array $files The FILES array
	 * @return boolean Upload okay?
	 */

	function check_files($files)
	{
		foreach ($this->fields as $fld) {
			if ($fld['type'] == 'file') {
			   	if (isset($fld['required']) && $fld['required'] == 1) {
					if (!isset($files[$fld['name']])) {
						return FALSE;
					}
					elseif ($files[$fld['name']]['error'] != 0) {
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * check_requireds()
	 *
	 * This iterates through the fields and checks for any which have
	 * the "required" component set. These are checked against the POST
	 * array (passed in) and if there are discrepancies, it returns
	 * FALSE. Else, TRUE. To save execution cycles, it stops at the
	 * first failure.
	 *
	 * @param array $post The POST array
	 * @return boolean All required fields are there?
	 */

	function check_requireds($post)
	{
		foreach ($this->fields as $fld) {
			if ($fld['type'] != 'file') {
				if (isset($fld['required']) && $fld['required'] == 1) {
					if (!isset($post[$fld['name']])) {
						return FALSE;
					}
					elseif (!is_array($post[$fld['name']])) {
						if (strlen($post[$fld['name']]) == 0) {
							return FALSE;
						}
					}
					elseif (empty($post[$fld['name']])) { // is an array
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}

	function version()
	{
		return 3.1;
	}
}

