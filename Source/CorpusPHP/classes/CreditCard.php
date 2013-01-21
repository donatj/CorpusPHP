<?php

/**
* Class Representing a Credit Card
* @todo Clean Up OSC Dependancy
* @package CorpusPHP
* @subpackage Processing
* @author Jesse G. Donat
*/
class CreditCard {
	
	public $type, $number, $expiry_month, $expiry_year, $cvv2;
	public $valid = false;

	function __construct($number, $expiry_m, $expiry_y, $cvv2 = '') {
		$this->valid = $this->parse($number, $expiry_m, $expiry_y, $cvv2) + $this->is_valid() > 0;
	}
	
	private function parse($number, $expiry_m, $expiry_y, $cvv2) {
		$this->number = ereg_replace('[^0-9]', '', $number);

		if (ereg('^4[0-9]{12}([0-9]{3})?$', $this->number)) {
			$this->type = 'Visa';
			if (ACC_VISA == 'false') { return -1;}
		} elseif (ereg('^5[1-5][0-9]{14}$', $this->number)) {
			$this->type = 'Master Card';
			if (ACC_MASTER == 'false'){ return -1;}
		} elseif (ereg('^3[47][0-9]{13}$', $this->number)) {
			$this->type = 'American Express';
			if (ACC_AMEX== 'false'){return -1;}
		} elseif (ereg('^3(0[0-5]|[68][0-9])[0-9]{11}$', $this->number)) {
			$this->type = 'Diners Club';
			if (ACC_DINERS == 'false'){ return -1;}
		} elseif (ereg('^6011[0-9]{12}$', $this->number)) {
			$this->type = 'Discover';
			if (ACC_DISC == 'false'){ return -1;}
		} elseif (ereg('^(3[0-9]{4}|2131|1800)[0-9]{11}$', $this->number)) {
			$this->type = 'JCB';
			if (ACC_JCB == 'false'){ return -1;}
		} elseif (ereg('^5610[0-9]{12}$', $this->number)) {
			$this->type = 'Australian BankCard';
			if (ACC_AUS == 'false'){ return -1;}
		} else {
			return -1;
		}

		if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
			$this->expiry_month = $expiry_m;
		} else {
			return -2;
		}

		$current_year = date('Y');
		if(strlen( $expiry_y ) == 2) { $expiry_y = substr($current_year, 0, 2) . $expiry_y; }
		if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
			$this->expiry_year = $expiry_y;
		} else {
			return -3;
		}

		if ($expiry_y == $current_year) {
			if ($expiry_m < date('n')) {
				return -4;
		}
		}
		/*
		if  ( (strlen($cvv2) < 3) or (strlen($cvv2) > 4)) {
		  return -5;
		}
		*/
	}
	
	public function mask() {
		return str_repeat( 'X', strlen($this->number) - 4 ) . substr( $this->number, -4 );
	}

	private function is_valid() {
		$cardNumber = strrev($this->number);
		$numSum = 0;

		for ($i=0; $i<strlen($cardNumber); $i++) {
			$currentNum = substr($cardNumber, $i, 1);

			// Double every second digit
			if ($i % 2 == 1) {
				$currentNum *= 2;
			}

			// Add digits of 2-digit numbers together
			if ($currentNum > 9) {
				$firstNum = $currentNum % 10;
				$secondNum = ($currentNum - $firstNum) / 10;
				$currentNum = $firstNum + $secondNum;
			}

			$numSum += $currentNum;
		}

		// If the total has no remainder it's OK
		return (int)($numSum % 10 == 0);
	}
}
