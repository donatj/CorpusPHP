<?

/**
* Form Elements
*
* @package CorpusPHP
* @subpackage Output
* @author Jesse G. Donat
* @version 1.3.1
* @todo finish documenting
* @todo Rework such that it extends elements
*/
class fe extends FormElements {} class FormElements {

	/**
	* Used to build input tag style inputs
	*
	* @param string $type the type of input
	* @param string $name the name of the input
	* @param string|bool|array $value the value of the input, if an array the value of the key of $name, if boolean true autopopulates from $_POST
	* @param bool $checked whether the input is checked
	* @param string $params extra html paramaters
	* @param bool $raw whether
	*/
	static private function __input($type, $name, $value, $checked, $params, $raw) {
		if(is_array( $value ) ) { $value = $value[$name]; }
		elseif($value === true) { $value = $_POST[$name]; }
		
		if( !$raw ) $value = htmlE($value);
		return '<input name="'.$name.'" '.( $checked ? 'checked="checked"' : '').' type="'.$type.'" value="'.$value.'" '.$params.' />' . "\n";
	}


	/**
	* Creates a Dropdown from a passed in array of key value pairs
	*
	* @param array $data_array array of key value pairs whereas the key is the option value, and value is the innerHTML
	* @param string $name
	* @param string|bool|array $selected the value of the selected option, if an array the value of the key of $name, if boolean true autopopulates from $_POST
	* @param string $params
	* @param bool|string $blank Value for an initial blank value, does not display if false
	* @param bool $strict determines whether an int value should be considered an auto and not a value, or ignored
	*/
	static function DropdownFromArray($data_array, $name, $selected, $params = '', $blank = false, $strict = false) {
		if(is_array( $selected ) ) { $selected = $selected[$name]; }
		elseif($selected === true) { $selected = $_POST[$name]; }

		$str = '';
		$i   = 0;
		if( $blank ) $str = '<option value="">'.$blank.'</option>';
		foreach($data_array as $k => $v) {
			if(is_int($k) && !$strict) { $value = $v; } else{ $value = $k; }
			$str .= '<option value="'.htmlE($value).'" '.($value == $selected ? 'selected="Selected"' :'').'>'.htmlE($v).'</option>';
			if(++$i % 4 == 0) { $str .= "\n"; }
		}
		return '<select name="'.$name.'" '.$params.">\n" . $str . "\n</select>";
	}

	/**
	* Builds a dropdown from a Query
	* 
	* @see self::DropdownFromArray()
	*
	* @param resource|string $sql Query or Query Resource where the first column is used as the option value and the second the option text
	*/
	static function DropdownFromSql($sql, $name, $selected, $params = '', $blank = false) {
		$data = db::fetch( $sql, db::KEYVALUE );
		return self::DropdownFromArray( $data, $name, $selected, $params, $blank, true );
	}

	static function ChecboxesFromSql($sql, $name, $selected, $labelParams = '', $label_post = '<br style="clear: both;"/>', $labelAfter = true ) {
		$qry = db::query($sql);
		$data = array();
		while( $row = mysql_fetch_array($qry) ) {
			$d = array('value' => $row[0], 'label_post' => $label_post);
			if( $labelAfter ) {
				$d['post_label'] = $row[1];
			}else{
				$d['pre_label'] = $row[1];
			}
			if( is_array( $selected ) && in_array( $d['value'], $selected ) ) { $d['checked'] = true; }
			$data[] = $d;
		}
		return self::CheckboxesFromArray( $data, $name, $labelParams );
	}

	/**
	* Outputs a Dropdown of States...
	*
	* @see self::DropdownFromArray() 
	*/
	static function StatesDropdown($name, $selected, $params = '', $blank = false) {
		return self::DropdownFromSql("select zone_code, zone_name From zones Where zone_country_id = 223 And us_territory = 0 Order By zone_name", $name, $selected, $params, $blank);
	}

	static function NumericRangeDropdown($start, $end, $name, $selected, $params = '', $blank = false, $step = 1) {
		return self::DropdownFromArray(range($start, $end, $step), $name, $selected, $params, $blank);
	}

	static function Checkbox($name, $checked = false, $value="checked", $params = 'style="width: auto"', $raw = false) {
		return self::__input('checkbox', $name, $value, $checked, $params, $raw);
	}

	static function HiddenField($name, $value, $params = '', $raw = false) {
		return self::__input('hidden', $name, $value, false, $params, $raw);
	}

	static function Textbox($name, $value, $params = '', $raw = false) {
		return self::__input('text', $name, $value, false, $params, $raw);
	}

	static function Password($name, $value, $params = '', $raw = false) {
		return self::__input('password', $name, $value, false, $params, $raw);
	}

	static function RadioButton($name, $checked = false, $value="checked", $params = '', $raw = false) {
		return self::__input('radio', $name, $value, $checked, $params, $raw);
	}

	/**
	* Builds a set of checkboxes from an array...
	*
	* @param array $data_array
	* @param string|bool $name
	* @param string $labelParams
	*/
	static function CheckboxesFromArray($data_array, $name = false, $labelParams = '') {
		$data = '';

		foreach($data_array as $k => $v) {
			$data .= $v['label_pre'] . '<label '.$labelParams.'>' . $v['pre_label']  .
				self::Checkbox(firstNotEmpty($name, $k),
					(bool)$v['checked'],
					firstNotEmpty($v['value'], valOrFalseIfNumeric($k), 'checked'),
					firstNotEmpty($v['params'], 'style="width: auto"')) .
				$v['post_label'] . '</label>' . $v['label_post'] . "\n";
		}
		return $data;
	}

	/**
	* Builds a set of radio buttons from an array
	*
	* @param array $data_array
	* @param string|bool $name
	* @param mixed $checkKey
	* @param string $labelParams
	*/
	static function RadioButtonsFromArray($data_array, $name = false, $checkKey = false, $labelParams = '') {
		$data = '';

		foreach($data_array as $k => $v) {
			$data .= $v['label_pre'] . '<label '.$labelParams.'>' . $v['pre_label']  .
				self::RadioButton(firstNotEmpty($name, $k),
					firstNotEmpty($checkKey == $k,(bool)$v['checked']),
					firstNotEmpty($v['value'], $k),
					$v['params']) .
				$v['post_label'] . '</label>' . $v['label_post'] . "\n";
		}
		return $data;
	}
	
	/**
	* Builds a Textarea
	* 
	* @param string $name
	* @param string|bool|array $value
	* @param string $params
	* @param integer $rows
	* @param integer $cols
	* @param bool $raw
	*/
	static function Textarea($name, $value, $params = '', $rows = 3, $cols = 40, $raw = false) {
		if(is_array( $value ) ) { $value = $value[$name]; }
		elseif($value === true) { $value = $_POST[$name]; }
		
		if( !$raw ) $value = htmlE($value);
		return '<textarea name="'.$name.'" rows="'.(int)$rows.'" cols="'.(int)$cols.'" '.$params.' >'.$value.'</textarea>';
	}

}