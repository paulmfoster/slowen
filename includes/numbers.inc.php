<?php

/*
if (!defined('DECIMALS')) {
	define('DECIMALS', 2);
}
if (!defined('DECIMAL_SYMBOL')) {
	define('DECIMAL_SYMBOL', '.');
}
 */

/**
 * Converts fractional dollar amounts to pennies. Works for any
 * currency.
 *
 * This function does not round or truncate numbers. It turns a number
 * like this: 123.45 to this: 12345. It assumes you want to store
 * decimal numbers in a database as integers. Signs are allowed, but are
 * only returned if negative.
 *
 * Incidentally, this doesn't work in PHP 7.3:
 *
 * $n = intval(20.15 * pow(10, DECIMALS)); // == 20.14
 *
 * @param string The number you wish to convert
 * @return string The converted number
 */

function dec2int($number)
{
	// trim first
	$number = trim($number);
	if (empty($number)) {
		return '0';
	}

	// handle signs
	$neg = '';
	if ($number[0] == '+') {
		$number = ltrim($number, '+');
	}
	elseif ($number[0] == '-') {
		$number = ltrim($number, '-');
		$neg = '-';
	}

	// test for decimal point
	$decpt = strpos($number, DECIMAL_SYMBOL);
	if ($decpt === FALSE) {
		// no decimal point
		$unsigned = $number . str_repeat('0', DECIMALS);
	}
	else {
		// decimal point present
		$len = strlen($number);

		$left = substr($number, 0, $decpt);
		$right = substr($number, $decpt + 1);

		$rlen = strlen($right);
		$slop = $rlen - DECIMALS;

		if ($slop > 0) {
			// too many decimals; truncate
			$right = substr($right, 0, DECIMALS);
		}
		elseif ($slop < 0) {
			// pad on the right with zeroes
			$right = $right . str_repeat('0', abs($slop));
		}
		// may be leading zeroes, so remove them
		$unsigned = ltrim($left . $right, '0');
		// if the number was all zeroes, the above will make it blank, so
		// compensate for that
		if (strlen($unsigned) == 0) {
			$unsigned = '0';
		}
	}

	$new = $neg . $unsigned;

	return $new;
}

/*

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

 */

function int2dec($number = 0)
{
	// don't process if the number is already decimalized
	if (strpos($number, DECIMAL_SYMBOL) !== FALSE) {
		return $number;
	}

	if (strpos($number, '-') === 0) {
		$sign = '-';
		$number = substr($number, 1);
	}
	else {
		$sign = '';
	}

	$len = strlen($number);
	$short = $len - DECIMALS - 1;
	if ($short < 0) {
		$number = str_repeat('0', abs($short)) . $number;
		$len -= $short;
	}

	$left = substr($number, 0, $len - DECIMALS);
	$right = substr($number, $len - DECIMALS);

	return $sign . $left . '.' . $right;
}


/**
 * This converts a string representation of an integer
 * into a string representation of money (in the U.S.,
 * for example, 200 would be 2.00).
 */

/*
function int2dec($number)
{
	// don't process if the number is already decimalized
	if (strpos($number, DECIMAL_SYMBOL) !== FALSE)
		return $number;

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
 */
