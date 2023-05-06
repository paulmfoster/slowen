<?php

if (!defined('DECIMALS')) {
	define('DECIMALS', 2);
}
if (!defined('DECIMAL_SYMBOL')) {
	define('DECIMAL_SYMBOL', '.');
}

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

/**
 * Convert integer value to decimal (or other).
 *
 * This routine assumes floats are stored as integers with some number of
 * decimal places. Using the constant DECIMALS, which tells us how many
 * decimal places are needed, we parse the integer and insert a decimal
 * point where indicated.
 *
 * NOTE: The user is advised to use this routine at the point of display,
 * not before. Otherwise, PHP can integerize numbers when converted
 * to decimals if they are equivalent to integers.
 *
 * @param integer The number to convert
 * @return string The number converted to decimal
 */

function int2dec($number)
{
    if (strpos($number, DECIMAL_SYMBOL) != FALSE)
        return NULL;

    $multiplier = pow(10, DECIMALS);

    $left = intdiv($number, $multiplier);
    $right = $number % $multiplier;
    $str = sprintf("%d.%*d", $left, DECIMALS, abs($right));

    return $str;
}

/*
function int2dec($number = 0)
{
	// don't process if the number is already decimalized
	if (strpos($number, DECIMAL_SYMBOL) !== FALSE) {
		return $number;
	}

    $number = trim((string) $number);
    // establish sign
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

	return $sign . $left . DECIMAL_SYMBOL . $right;
}
 */

