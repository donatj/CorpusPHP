<?php
/**
 * @author Greg Crane
 * @version 1.1
 * @package Utilities
 */
/**
 * This class is used to clean and check data from forms before mailing or inserting into database.
 */
class FormUtility {
	/**
	 * @var array $filtered $_POST vars stripped of nasties
	 * @var array $html filtered vars cleaned for html output
	 * @var array $mysql vars filtered for input into database
	 * @var string $errorMessage
	 */
	var $filtered = array();
	var $html = array();
	var $mysql = array();
	var $errorMessage;

	function FormUtility(){
		$this->injectionFilter();
		$this->escapeHtml();
		$this->escapeSql();
	}

	/**
	 * function to remove newlines, returns, etc from input and replace with a space
	 */
	function injectionFilter() {
		$injections = array('/(\n+)/i',
							'/(\r+)/i',
							'/(\t+)/i',
							'/(%0A+)/i',
							'/(%0D+)/i',
							'/(%08+)/i',
							'/(%09+)/i');

		foreach($_POST as $key=>$value){
			$value = trim($value);
			$this->filtered[$key] = preg_replace($injections,' ',$value);
		}
	}

	/**
	 * function to escape output for html display
	 */
	function escapeHtml(){
		foreach($this->filtered as $key=>$value){
			$this->html[$key] = htmlentities($value);
		}
	}

	/**
	 * function to escape output for database insertion
	 */
	function escapeSql(){
		foreach($this->filtered as $key=>$value){
			$this->mysql[$key] = mysql_real_escape_string($value);
		}
	}

	/**
	 * function to check for correctly formed email address
	 * @param string $email
	 */
	function validateEmail($email) {
		if($this->requiredField($email)){
			if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $this->filtered[$email])) {
				$this->errorMessage .= "Email is not in the correct format.<br/>\n";
				return false;
			}
		}
		return true;
	}

	/**
	 * function to check for a valid email domain
	 * @param string $email
	 */
	function checkMailDomain($email) {
		list($userName, $mailDomain) = split("@", $this->filtered[$email]);
		if (!checkdnsrr($mailDomain, "MX")) {
			$this->errorMessage .= "Email address appears to be invalid, please check.<br/>\n";
		}
	}

	/**
	 * function to check that required fields are populated
	 * @param string $formField
	 */
	function requiredField($formField, $prettiful_name = false) {
		$fieldValue = trim($_POST[$formField]);
		$formField = preg_replace('/_/',' ',$formField);
		if (strip_tags($fieldValue) == '') {
			$this->errorMessage .= ($prettiful_name ? $prettiful_name : ucwords(strtolower($formField))) . " appears to be incomplete.<br/>\n";
			return false;
		}
		return true;
	}

	//here down added by Jesse Donat for better all around goodness
	function requiredFields($formFieldArray) {
		foreach($formFieldArray as $key => $v) {
			$this->requiredField($v, is_numeric($key) ? false : $key);
		}
	}

	function requiredValue($formField, $value, $prettiful_name = false, $prettiful_value = false) {
		$fieldValue = trim($_POST[$formField]);
		$formField = preg_replace('/_/',' ',$formField);
		if ($fieldValue != $value) {
			$this->errorMessage .= firstNotEmpty( $prettiful_name, ucwords(strtolower($formField)) ) . ' must be &ldquo;' . firstNotEmpty( $prettiful_value, ucwords(strtolower($value)) ) . '&rdquo; to continue;';
			return false;
		}
		return true;
	}

	function validateLength($formField, $length = 5,  $prettiful_name = false) {
		$fieldValue = trim($_POST[$formField]);
		if(strlen($fieldValue) < $length) {
			$this->errorMessage .= ($prettiful_name ? $prettiful_name : ucwords(strtolower($formField))) . ' ' . ' is too short.<br/>';
			return false;
		}
		return true;
	}

	function validateUsername($formField, $prettiful_name = false, $msg = "appears to be in an invalid format, please ensure it is alphanumeric.<br/>") {
		$fieldValue = trim($_POST[$formField]);
		if($this->requiredField($formField) && $this->validateLength($formField)) {
			if( ereg('[^A-Z^a-z^0-9]', $fieldValue) ) {
				$this->errorMessage .= ($prettiful_name ? $prettiful_name : ucwords(strtolower($formField))) . ' ' . $msg;
				return false;
			}else{
				return true;
			}
		}
		return false;
	}

	function validatePassword($formField, $prettiful_name = false) {
		$fieldValue = trim($_POST[$formField]);
		return $this->validateUsername($formField, $prettiful_name);
	}

	function validateXmatchesY($x, $y, $prettiful_name_x = false, $prettiful_name_y = false) {
		$fieldValueX = trim($_POST[$x]);
		$fieldValueY = trim($_POST[$y]);
		if($fieldValueX == $fieldValueY) {
			return true;
		}else{
			$this->errorMessage .= ($prettiful_name_x ? $prettiful_name_x : ucwords(strtolower($x))) . ' does not match ' . ($prettiful_name_y ? $prettiful_name_y : ucwords(strtolower($y))) . '<br/>';
			return false;
		}
	}

	function validateXmatchesYi($x, $y, $prettiful_name_x = false, $prettiful_name_y = false) {
		return $this->validateXmatchesY(strtolower($x),strtolower($y),$prettiful_name_x,$prettiful_name_y);
	}

}