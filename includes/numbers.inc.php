<?php

/**
 * @package grotblog
 * @copyright 2016, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 1.0
 */

define('NUMBERS_INC_PHP', TRUE);

/**
 * Turns a number with a possible decimal point into an integer by
 * effectively multiplying it by the number of decimals in use by
 * this locale. This is all done with string manipulation for two 
 * reasons: 1) to avoid any possible distortions introduced by rounding 
 * or straight mathematical manipulation; 2) because this function is 
 * usually used on numbers returning from user input, which are always 
 * regurned as strings.
 *
 * Note: Don't let users get cute by using parentheses and such to 
 * indicate negative numbers. Negative numbers should be indicated by a 
 * single dash or minus sign to the immediate left of the left-most 
 * digit of the number. Also, tell them not to include currency symbols 
 * as part of the number string. This function won't care, but the 
 * database back end may well care.
 *
 * @param string $number The number to be modified.
 *
 * @return string The number, pictured as an integer.
 */

function dec2int($number)
{
	// trim first
	$number = trim($number);
	if (empty($number)) {
		return 0;
	}

	// handle signs
	if ($number[0] == '+' || $number[0] == '-') {
		$vlen = strlen($number);
		$sign = 1;
		for ($v = 0; $v < $vlen; $v++) {
			if ($number[$v] == '-')
				$sign = -$sign;
			elseif ($number[$v] == '+')
				continue;
			else
				break;
		}

		// We assume $v will never get to $vlen - 1.
		// That would mean the string was all just signs
		// therefore not a real number

		$start = $v;
		if ($sign < 0)
			$number = '-' . substr($number, $start);
		else
			$number = substr($number, $start);

	}

	$posn = strpos($number, DECIMAL_SYMBOL);
	if ($posn === FALSE) {
		$suffix = str_repeat('0', DECIMALS);
		return $number . $suffix;
	}
	else {
		$left = substr($number, 0, $posn);
		$right = substr($number, $posn + 1);
		$rlen = strlen($right);
		if ($rlen > DECIMALS) {
			$suffix = substr($right, 0, DECIMALS);
			return $left . $suffix;
		}
		elseif ($rlen == DECIMALS) {
			return $left . $right;
		}
		else {
			// less than two digits
			$add = DECIMALS - $rlen;
			$str = str_repeat('0', $add);
			return $left . $right . $str;
		}
	}
}

/**
 * This converts a string representation of an integer
 * into a string representation of money (in the U.S.,
 * for example, 200 would be 2.00).
 */

function int2dec($number)
{
	if (!empty($number)) {
		$slen = strlen($number);

		$rlen = $slen - DECIMALS;
		if ($rlen < 0) {
			$rlen = abs($rlen);
			$pad = str_repeat('0', $rlen);
			$right = $pad . $number;
		}
		else {
			$right = substr($number, $slen - DECIMALS, DECIMALS);
		}
		$left = substr($number, 0, $slen - DECIMALS);
		$new_number = $left . DECIMAL_SYMBOL . $right;
	}
	else {
		$new_number = '0' . DECIMAL_SYMBOL . str_repeat('0', DECIMALS);
	}
	return $new_number;
}

