<?php


/**
 * Form Class
 *
 * This class primarily displays form controls
 *
 */

class form
{
	function __construct($fields)
	{
		$this->fields = $fields;
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

		if (isset($fld['size'])) {
			$str .= $this->key_value('size', $fld['size']);
		}

		if (isset($fld['maxlength'])) {
			$str .= $this->key_value('maxlength', $fld['maxlength']);
		}

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		$str .= '/>' . PHP_EOL;

		echo $str;
	}

	function password($field_name, $content = NULL)
	{
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

		$str .= '/>' . PHP_EOL;

		echo $str;
	}

	function select($field_name, $select_value = NULL)
	{
		$fld = $this->fields[$field_name];
		$opts = $this->fields[$field_name]['options'];

		$str = '<select ';
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);
		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}
		if (isset($fld['size'])) {
			$str .= $this->key_value('size', $fld['size']);
		}
		if (isset($fld['multi'])) {
			if ($fld['multi'] == 1) {
				$str .= 'multiple ';
			}
		}
		$str .= '>' . PHP_EOL;

		foreach ($opts as $option) {
			$str .= '<option ';
			$str .= $this->key_value('value', $option['val']);

			if (!is_null($select_value)) {
				if (!is_array($select_value)) {
					if ($option['val'] == $select_value) {
						$str .= 'selected="selected" ';
					}
				}
				else {

					$max = count($select_value);
					for ($i = 0; $i < $max; $i++) {
						if ($option['val'] == $select_value[$i]) {
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

	function radio_option($str, $option, $checked_value)
	{
		$str .= $this->key_value('value', $option['val']);

		if ($option['val'] == $checked_value) {
			$str .= 'checked';
		}
		$str .= '/>' . PHP_EOL;

		return $str;
	}

	function radio($field_name, $checked_value = NULL)
	{

		$fld = $this->fields[$field_name];
		$opts = $fld['options'];
		$direction = $fld['direction'];

		$str = '';
		foreach ($opts as $option) {

			if ($direction === 'L') {
				$str .= $option['lbl'] . '&nbsp;';

				$str .= '<input ';
				$str .= $this->key_value('type', 'radio');
				$str .= $this->key_value('name', $fld['name']);
				$str .= $this->key_value('value', $option['val']);
				
				if (isset($fld['class'])) {
					$str .= $this->key_value('class', $fld['class']);
				}

				if ($option['val'] == $checked_value) {
					$str .= 'checked="checked" ';
				}
				
				$str .= '/>' . PHP_EOL;
			}
			elseif ($direction === 'R') {

				$str .= '<input ';
				$str .= $this->key_value('type', 'radio');
				$str .= $this->key_value('name', $fld['name']);
				$str .= $this->key_value('value', $option['val']);
				
				if (isset($fld['class'])) {
					$str .= $this->key_value('class', $fld['class']);
				}

				if ($option['val'] == $checked_value) {
					$str .= 'checked';
				}
				
				$str .= '/>' . PHP_EOL;
				$str .= '&nbsp;' . $option['lbl'];
			}
			elseif ($direction == 'LV') {
				$str .= $option['lbl'] . '&nbsp;';

				$str .= '<input ';
				$str .= $this->key_value('type', 'radio');
				$str .= $this->key_value('name', $fld['name']);
				$str .= $this->key_value('value', $option['val']);

				if (isset($fld['class'])) {
					$str .= $this->key_value('class', $fld['class']);
				}

				$str = $this->radio_option($str, $option, $checked_value);
				$str .= '<br/>' . PHP_EOL;
			}
			elseif ($direction == 'RV') {
				
				$str .= '<input ';
				$str .= $this->key_value('type', 'radio');
				$str .= $this->key_value('name', $fld['name']);
				$str .= $this->key_value('value', $option['val']);

				if (isset($fld['class'])) {
					$str .= $this->key_value('class', $fld['class']);
				}

				$str = $this->radio_option($str, $option, $checked_value);
				$str .= '&nbsp;' . $option['lbl'];
				$str .= '<br/>' . PHP_EOL;
			}
		}
		
		echo $str;
	}

	function checkbox($field_name, $checked_value = NULL)
	{
		$fld = $this->fields[$field_name];

		$str = '<input type="checkbox" ';
		$str .= $this->key_value('name', $fld['name']);
		$str .= $this->key_value('id', $fld['name']);
		$str .= $this->key_value('value', $fld['value']);

		if (isset($fld['class'])) {
			$str .= $this->key_value('class', $fld['class']);
		}

		if (!is_null($checked_value)) {
			if ($fld['value'] == $checked_value) {
				$str .= 'checked';
			}
		}

		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function file($field_name)
	{
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
		if ($content === '') {
			return '';
		}

		$parms = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'hidden');
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);
		$str .= $this->key_value('value', $content);
		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function textarea($field_name, $content = NULL)
	{
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

		$str .= '>' . PHP_EOL;

		if (!is_null($content)) {
			$str .= $content;
		}
		$str .= '</textarea>' . PHP_EOL;

		echo $str;
	}

	static function button($legend, $link)
	{
		$str = '<a href="' . $link . '">';
		$str .= '<button type="button">';
		$str .= $legend;
		$str .= '</button></a>' . PHP_EOL;

		echo $str;
	}

	static function abandon($link)
	{
		$str = '<a href="' . $link . '"><button type="button">Abandon</button></a>' . PHP_EOL;
		echo $str;
	}

	function submit($field_name)
	{
		$parms = $this->fields[$field_name];

		$str = '<input ';
		$str .= $this->key_value('type', 'submit');
		$str .= $this->key_value('name', $parms['name']);
		$str .= $this->key_value('id', $parms['name']);
		$str .= $this->key_value('value', $parms['value']);
		$str .= '/>' . PHP_EOL;
		echo $str;
	}

	function output($field_name, $content = NULL)
	{
		$field = $this->fields[$field_name];
		$this->{$field['type']}($field_name, $content);
	}

	// =======================================================
	// Other Routines
	// =======================================================

	function check_requireds($post)
	{
		$errors = 0;
		foreach ($this->fields as $fld_arr) {
			if (isset($fld_arr['required']) && $fld_arr['required'] == 1) {
				if (!isset($post[$fld_arr['name']])) {
					$errors++;
				}
			}
		}
		return $errors ? FALSE : TRUE;
	}

	/**
	 * table2html()
	 *
	 * This function outputs a string which shows a table of the field
	 * contents of records from a database. It takes an indexed array of
	 * records, each of which is an associative array of field names =>
	 * contents. Records are in whatever order your SQL statement fetched
	 * them and are displayed in the fetched order.
	 *
	 * @param array $records indexed/assoc array of records
	 *
	 * @return string All the HTML for a table display.
	 *
	 */

	static public function table2html($records)
	{
		$max_records = count($records);

		$str = '<style>' . PHP_EOL;
		$str .= '.sansserif {font-family: verdana, helvetica, arial, sans-serif;}' . PHP_EOL;
		$str .= '.row0 {background-color: #FFFFFF;}' . PHP_EOL;
		$str .= '.row1 {background-color: #CCCCCC;}' . PHP_EOL;
		$str .= '</style>' . PHP_EOL;

		$str .= '<div class="sansserif">' . PHP_EOL;
		$str .= '<table rules="all" border="1">' . PHP_EOL;
		for ($i = 0; $i < $max_records; $i++) {

			// headers: field names
			if ($i == 0) {
				$keys = array_keys($records[$i]);
				$max_fields = count($keys);
				$str .= '<tr class="row' . ($i & 1) . '">';
				for ($j = 0; $j < $max_fields; $j++) {
					$str .= '<th>';
					$str .= $keys[$j];
					$str .= '</th>';
				}
				$str .= '</tr>' . PHP_EOL;
			}

			// records
			$values = array_values($records[$i]);
			$str .= '<tr class="row' . ($i & 1) . '">';
			for ($j = 0; $j < $max_fields; $j++) {
				$str .= '<td>';
				$str .= $values[$j];
				$str .= '</td>';
			}
			$str .= '</tr>' . PHP_EOL;
		}
		$str .= '</table>' . PHP_EOL;
		$str .= '</div>' . PHP_EOL;

		return $str;
	}

	function version()
	{
		return 1.5;
	}
}

/*
 * Regexes for various field types
 * Not currently used.
 * HTML5 includes many of these field types, but support
 * in Firefox et al is spotty.
 *
 * Sanitization:
 *
 * 'alpha' => "%[^a-zA-Z]%"
 * 'alphanum' => "%[^a-zA-Z0-9]%"
 * 'email' => "%[^a-zA-Z\.\-\_\@0-9\+]%"
 * 'name' => "%[^a-zA-Z\., ]%"
 * 'phone' => "%[^0-9\.\- ]%"
 * 'date' => "%[^0-9\.\-/]%"
 * 'text' => "%[^a-zA-Z0-9 \.!-/\?]%")
 *
 * Verification:
 *
 * 'ccardno' => '/^(4\d{12})|(((4|3)\d{3})|(5[1-5]\d{2})|(6011))(\d{12})|(3[4,7]\d{13})|(30[0-5]\d{1}|(36|38)\d(2))(\d{10})|((2131|1800)|(2014|2149))(\d{11})$/'
 * 'email' => '^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$',
 * 'integer' => '^[0-9]*$',
 * 'float' => '^[-+]?[0-9]+[.]?[0-9]*([eE][-+]?[0-9]+)?$',
 *
 */


/* TESTING CODE

$fruit_options = array(
	array('lbl' => 'Apples', 'val' => 'apples'),
	array('lbl' => 'Peaches', 'val' => 'peaches'),
	array('lbl' => 'Grapes', 'val' => 'grapes'),
	array('lbl' => 'Plums', 'val' => 'plums'),
	array('lbl' => 'Pears', 'val' => 'pears')
);

$car_options = array(
	array('lbl' => 'Chevrolet', 'val' => 'chevrolet'),
	array('lbl' => 'Oldsmobile', 'val' => 'oldsmobile'),
	array('lbl' => 'Cadillac', 'val' => 'cadillac'),
	array('lbl' => 'Toyota', 'val' => 'toyota'),
	array('lbl' => 'Nissan', 'val' => 'nissan'),
	array('lbl' => 'Mercedes', 'val' => 'mercedes'),
	array('lbl' => 'Audi', 'val' => 'audi')
);

$sitetype_options = array(
	array('lbl' => 'Customer', 'val' => 'C'),
	array('lbl' => 'Internal', 'val' => 'Q')
);

$hostbyus_options = array(
	array('lbl' => 'Yes', 'val' => 1),
	array('lbl' => 'No', 'val' => 0)
);

$fields = array(
	'sitename' => array(
		'name' => 'sitename', 
		'type' => 'text', 
		'size' => 25, 
		'maxlength' => 25),
	'fruits' => array(
		'name' => 'fruits',
		'type' => 'select',
		'size' => 4,
		'multi' => 1,
		'options' => $fruit_options),
	'cars' => array(
		'name' => 'cars',
		'type' => 'select',
		'options' => $car_options),
	'sitetype' => array(
		'name' => 'sitetype',
		'type' => 'select',
		'options' => $sitetype_options),
	'hostbyus1' => array(
		'name' => 'hostbyusL',
		'type' => 'radio',
		'class' => 'alfa',
		'options' => $hostbyus_options),
	'hostbyus2' => array(
		'name' => 'hostbyusR',
		'type' => 'radio',
		'class' => 'bravo',
		'options' => $hostbyus_options),
	'hostbyus3' => array(
		'name' => 'hostbyusLV',
		'type' => 'radio',
		'class' => 'charlie',
		'options' => $hostbyus_options),
	'hostbyus4' => array(
		'name' => 'hostbyusRV',
		'type' => 'radio',
		'class' => 'delta',
		'options' => $hostbyus_options),
	'modsbyus' => array(
		'name' => 'modsbyus',
		'type' => 'checkbox',
		'value' => 1,
		'sel' => 1),
	'MAX_FILE_SIZE' => array(
		'name' => 'MAX_FILE_SIZE',
		'type' => 'hidden'),
	'myfile' => array(
		'name' => 'myfile',
		'type' => 'file',
		'value' => 1000000),
	'comments' => array(
		'name' => 'comments',
		'type' => 'textarea',
		'rows' => 20,
		'cols' => 50,
		'wrap' => 'soft'),
	'update' => array(
		'name' => 'update',
		'type' => 'submit',
		'value' => 'Update')
);

$f = new form($fields);
echo '<table>' . PHP_EOL;

echo '<tr><td>Site Name</td>';
echo '<td>';
$f->text('sitename');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Cars</td>';
echo '<td>';
$f->select('cars');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Fruits</td>';
echo '<td>';
$f->select('fruits', array('apples', 'plums'));
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Fruits 2</td>';
echo '<td>';
$f->select('fruits', 'plums');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Site Type</td>';
echo '<td>';
$f->select('sitetype');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Test</td>';
echo '<td>';
echo '<input type="radio" name="test" value="0" checked/>';
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Hosted By Us 1</td>';
echo '<td>';
$f->radio('hostbyus1', 'L', 0);
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Hosted By Us 2</td>';
echo '<td>';
$f->radio('hostbyus2', 'R', 0);
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Hosted By Us 3</td>';
echo '<td>';
$f->radio('hostbyus3', 'LV', 0);
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Hosted By Us 4</td>';
echo '<td>';
$f->radio('hostbyus4', 'RV', 0);
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Mods By Us</td>';
echo '<td>';
$f->checkbox('modsbyus', 1);
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Your File</td>';
echo '<td>';
$f->file('myfile');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Your File</td>';
echo '<td>';
$f->output('myfile');
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '<tr><td>Comments</td>';
echo '<td>';
$f->textarea('comments', "I'm a pepper, you're a pepper");
echo '</td>';
echo '</tr>' . PHP_EOL;

echo '</table>' . PHP_EOL;

 */

