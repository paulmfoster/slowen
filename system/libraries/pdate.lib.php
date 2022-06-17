<?php

/**
 * @copyright  2012, 2017 Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 8.2
 */

if (!isset($date_template)) {
	$date_template = 'mdy|m/d/y|m-d-y';
}

// DEPENDENCIES: PHPv4+

// Error Series: 0200

/**
 * Date class
 *
 * THIS CLASS DOES NOT DEAL WITH TIMES, JUST DATES.
 *
 * This date class handles a variety of things that PHP date classes don't.
 * It uses julian dates to calculate dates and only uses native PHP date functions
 * to instantiate date objects when no other parameters are given.
 * It avoids regexps as much as possible for speed reasons, and uses
 * strpos() extensively for the same reason. Date format operators are:
 *
 * Y = 4 digit year
 * y = 2 digit year
 * m = 2 digit month
 * d = 2 digit day of month
 * i = ISO 8601, or ccyy-mm-dd
 * j = julian day
 * q = Quicken(R) date
 * r = RFC 2822 date (only value on get routine)
 * 
 * This class won't prevent users from doing stupid things. However,
 * date arrays do have an 'error' index pointing to a boolean value,
 * indicating if the date is okay.
 *
 * This class does *not* throw exceptions because I don't like them, nyah nyah.
 * It is written so it will work in PHP 4 environments
 *
 * VERSION 7 UPDATE (2017-05-17)
 *
 * Where before, the various components of the date were members of the
 * class, version 7 changes this completely. As of this version, the
 * class itself is simply a group of operators which typically rely on
 * a date array being passed in from somewhere. In other words, all
 * methods are now *static*. A date array looks like
 * this:
 *
 * date = array(
 * 	'j' => 0, (julian day)
 * 	'd' => 0, (day of the month, 1..31)
 * 	'm' => 0, (month, 1..12)
 * 	'y' => 0, (year, 4 digits)
 * 	'w' => 0, (day of the week, Sun = 0)
 * 	'dm' => 0, (days in the month 'm')
 * 	'dy' => 0, (day of the year, 1..366)
 * 	'wy' => 0, (week of the year, 1..52)
 * 	'error' => TRUE
 * 	);
 *
 * 	The class itself does not contain nor track any date. In fact, the
 * 	class is more or less static and nearly all the methods are also.
 *
 * 	A lot of the methods have been moved to internal functions inside
 * 	the normalize() function, since that's the only place they're
 * 	called, and they're not suitable for calling publically.
 *
 *  On the rewrite, some subtle errors were found and corrected.
 *
 *  In any case, this rewrite makes the class completely incompatible
 *  with any code which called the class before. It has also eliminated
 *  a lot code which is unnecessary, now that the date is a publically
 *  visible array.
 *
 *  Why rewrite the code this way? Just cuz! Well, mainly just to
 *  see what difference it would make to the code that uses it. It
 *  appears to simplify the interaction of other code with dates.
 *
 */

class pdate 
{

	// Week ending day
	const weday = 6; // Saturday

	/************************************************************
	 * CONSTRUCTORS
	 ***********************************************************/

	private static function blank_date()
	{
		$dt = array(
			'j' => 0,
			'd' => 0,
			'm' => 0,
			'y' => 0,
			'w' => 0,
			'dm' => 0,
			'wy' => 0,
			'dy' => 0,
			'error' => TRUE
		);

		return $dt;
	}

	/**
	 * Determines if a year is a leap year
	 *
	 * MITRE Corp pseudocode for leap year calculations:
	 * if (year mod 4 != 0)
	 *		{use 28 for days in February}
	 *	else if  (year mod 400 == 0)
	 *		{use 29 for days in February}
	 *	else if (year mod 100 == 0)
	 *		{use 28 for days in February}
	 *	else
	 *		{use 29 for days in February}
	 *
	 * @param integer $y Year
	 * @return boolean True if leap year, false otherwise
	 */

	static function is_leap_year($y)
	{
		if (($y % 4 == 0) && (($y % 100 != 0) || ($y % 400 == 0)))
			return true;
		else
			return false;
	}

	/**
	 * Return days in the month for a month/year
	 *
	 * @param integer $month
	 * @param integer $year
	 *
	 * @return integer Days in the month
	 */

	static function days_in_month($month, $year)
	{
		$mdays = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if ($month == 2) {
			if (self::is_leap_year($year))
				return 29;
			else
				return 28;
		}
		else
			return $mdays[$month - 1];
	}

	/*============================================================
	 * INTERNAL FUNCTIONS
	 *===========================================================*/

	/**
	 * Determines day of week for julian day
	 * Internal function
	 *
	 * @param integer $j Julian day
	 * @return integer Day of week, 0 = Sunday
	 */

	private static function jul2dow($j)
	{
		$n = (int) ($j + 0.5) + 1;

		return $n % 7;
	}

    /**
     * Return the julian day, given month, day and year
     *
     * There is a PHP function which does this: gregoriantojc(), but it's
     * not compiled into the version of PHP shipped with Arch (2022/06). So
     * this is the DIY version. Note: this code returns a FLOAT, not an
     * integer.
     *
	 * Original calculations from http://www.astro.uu.nl/~strous/AA/en/reken/juliaansedag.html
     *
     * @param integer year
     * @param integer month
     * @param integer day
     * @return float The julian date
	 */ 
	
	private static function g2j($year, $month, $day)
	{
	    if ($month < 3) {
	        $m = $month + 12;
	        $y = $year - 1;
	    }
	    else {
	        $m = $month;
	        $y = $year;
	    }
	    $c = 2 - floor($y / 100) + floor($y / 400);
	    $jday = floor(1461 * ($y + 4716) / 4) + floor(153 * ($m + 1) / 5) + $day + $c - 1524.5;
	    return $jday;
	}
	
    /**
     * Return year, month and day, given a julian date
     *
     * See comments from g2j().
     *
     * @param float julian date
     * @return array [year, month, day]
     */

	private static function j2g($jday)
	{
	    $p = floor($jday + 0.5);
	    $s1 = $p + 68569;
	    $n = floor(4 * $s1 / 146097);
	    $s2 = $s1 - floor((146097 * $n + 3) / 4);
	    $i = floor(4000 * ($s2 + 1) / 1461001);
	    $s3 = $s2 - floor(1461 * $i / 4) + 31;
	    $q = floor(80 * $s3 / 2447);
	    $e = $s3 - floor(2447 * $q / 80);
	    $s4 = floor($q / 11);
	    $m = $q + 2 - 12 * $s4;
	    $y = 100 * ($n - 49) + $i + $s4;
	    $d = $e + $jday - $p + 0.5;
	    return array($y, $m, $d);
	}

	/*============================================================
	 * END OF INTERNAL FUNCTIONS
	 *===========================================================*/

	/**
	 * Normalize function
	 *
	 * This function is designed to take a partially built date, which
	 * normally has either the year, month and day, or the julian day,
	 * and work out the rest of the values for the date array.
	 *
	 * Virtually all the functions this member would normally call have
	 * been moved internally, to be internal functions.
	 *
	 * @param array $dt A partially built pdate
	 * @param char $starting_point 'd' if the day/month/year are already
	 * in place, or 'j' if the jday is already in place.
	 *
	 * @return array The resulting date array.
	 */

	static function normalize($dt, $starting_point = 'd')
	{

		if ($starting_point === 'd') {
            $dt['j'] = self::g2j($dt['y'], $dt['m'], $dt['d']);
		}
		elseif ($starting_point === 'j') {
            $arr = self::j2g($dt['j']);
            $dt['y'] = $arr[0];
            $dt['m'] = $arr[1];
            $dt['d'] = $arr[2];
		}
		$dt['w'] = self::jul2dow($dt['j']);
		$dt['dm'] = self::days_in_month($dt['m'], $dt['y']);
        $timestamp = mktime(0, 0, 0, $dt['m'], $dt['d'], $dt['y']);
        $str = date('z^W', $timestamp);
		$arr = explode('^', $str);
		$dt['dy'] = $arr[0];
		$dt['wy'] = $arr[1];
		$dt['error'] = ! checkdate($dt['m'], $dt['d'], $dt['y']);

		return $dt;
	}

	/**
	 * Return today's date as pdate object/array.
     *
     * @return array pdate
	 */

	static function now()
	{
		$dt = self::blank_date();	

		$now = getdate(time());
		$dt['y'] = $now['year'];
		$dt['m'] = $now['mon'];
		$dt['d'] = $now['mday'];
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Return pdate object from year, month and day
	 *
	 * @param integer $yr Year
	 * @param integer $mo Month
	 * @param integer $da Day of month
	 *
	 * @return array pdate
	 */

	static function fromints($yr, $mo, $da) 
	{
		if ($yr < 100) {
			// POSIX X/Open standard "window"
			if ($yr >= 69 and $yr <= 99)
				$yr = 1900 + $yr;
			else
				$yr = 2000 + $yr;
		}

		$dt = self::blank_date();

		$dt['y'] = $yr;
		$dt['m'] = $mo;
		$dt['d'] = $da;

		$dt = self::normalize($dt, 'd');
		return $dt;
	}

	/**
	 * Return pdate based on julian day number
	 *
	 * @param integer $julian Julian day number
     * @return array pdate
	 */

	static function fromjul($julian)
	{
		$dt = self::blank_date();

		$dt['j'] = $julian;
		$dt = self::normalize($dt, 'j');

		return $dt;
	}

	/**
	 * Return pdate based on ISO 8601 date (ccyy-mm-dd)
	 *
	 * @param string $iso The ISO 8601 date
     * @return array pdate
	 */

	static function fromiso($iso)
	{
		$dt = self::blank_date();
        list($dt['y'], $dt['m'], $dt['d']) = explode('-', $iso);
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Return pdate based on Quicken(R) date
	 *
	 * @param string $date Quicken date string
     * @return array pdate
	 */

	static function fromqif($date)
	{
		$dt = self::blank_date();

		$date = str_replace('\'', '/', $date);
		$date = str_replace(' ', '', $date);

		if (!strpos($date, '/'))
			$parts = explode('-', $date);
		else
			$parts = explode('/', $date);

		$mo = $parts[0];
		$da = $parts[1];

		if (count($parts) == 2) {
			$today = localtime(time());
			$yr = 1900 + $today[5];
		}
		else
			$yr = $parts[2];

		if (strlen($yr) == 2)
			$yr = '20' . $yr;

		$mo = trim($mo);
		$da = trim($da);

		$dt['y'] = (int)$yr;
		$dt['m'] = (int)$mo;
		$dt['d'] = (int)$da;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

    /**
     * Return pdate object from Unix epoch seconds.
     *
     * @param integer Unix epoch seconds
     * @return pdate object
     */

    static function fromepoch($secs)
    {
        $d1 = date('Y-m-d', $secs);
        $d2 = explode('-', $dt);
        $d3 = self::fromints($d2[0], $d2[1], $d2[2]);
        return $d3; 
    }

	/**
	 * Called from set() with no params
	 * Routine returns pdate::now()
	 *
	 * @return array pdate array of today
	 */

	static private function set0()
	{
		return self::now();
	}

	/**
	 * Called by set when there are 2 passed params
	 *
	 * @param string $fmt Format date string is expected to be in
	 * @param string $date_str Date string
	 *
	 * @return array pdate
	 *
	 */

	static private function set2($fmt, $date_str)
	{

		$dt = '';
		if ($fmt == 'q') {
			$dt = self::fromqif($date_str);
		}
		elseif ($fmt == 'j') {
			$dt = self::fromjul($date_str);
		}
		elseif ($fmt === 'i') {
			$dt = self::fromiso($date_str);
		}
		if (!empty($dt))
			return $dt;

		// In this case, we assume that the template will
		// include the letters Y (or y), m and d, plus some
		// (consistent) delimiter

		// what if there are multiple templates?
		// Example: 'ymd|m-d-y|m/d/y'
		// assumption: each format would use different delimiters
		// assumption: within each format, the delimiters will be
		// the same
		if (strpos($fmt, '|') !== false) {
			// there are multiple formats
			$tmpls = explode('|', $fmt);

			// test date in turn against each template; which one fits?
			// NOTE: we only check for common delimiters,
			// nothing exotic
			$ct = count($tmpls);
			for ($i = 0; $i < $ct; $i++) {
				if (strpos($tmpls[$i], '/') !== false) {
					if (strpos($date_str, '/') !== false) {
						$fmt = $tmpls[$i];
						break;
					}
				}
				elseif (strpos($tmpls[$i], '-') !== false) {
					if (strpos($date_str, '-') !== false) {
						$fmt = $tmpls[$i]; 
						break;
					}
				}
				elseif (strpos($tmpls[$i], '.') !== false) {
					if (strpos($date_str, '.') !== false) {
						$fmt = $tmpls[$i]; 
						break;
					}
				}
				else {
					$fmt = $tmpls[$i];
				}
			}
		}


		$d = date_create_from_format($fmt, $date_str);
		$e = date_format($d, 'Ymd');

		$yr = (int) substr($e, 0, 4);
		$mo = (int) substr($e, 4, 2);
		$dy = (int) substr($e, 6, 2);

		$dt = self::fromints($yr, $mo, $dy);
		return $dt;

	}

	/**
	 * Called by set() with 3 parms,
	 * presumed to be year, month and day
	 *
	 * @param int $yr Year
	 * @param int $mo Month
	 * @param int $dy Day (of month)
	 *
	 * @return array pdate
	 *
	 */

	static function set3($yr, $mo, $dy)
	{
		$dt = self::blank_date();
		$dt['y'] = (int) $yr;
		$dt['m'] = (int) $mo;
		$dt['d'] = (int) $dy;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Date setter routine
	 *
	 * 0 parameters: set date to now
	 * 2 parameters: format, date string
	 * (Multiple formats allowed, separated by vertical bar)
	 * 3 parameters: year, month, day
	 *
	 * @param string|integer (optional) If string, presumed to be a format specifier, else year
	 * @param string|integer (optional) If string, presumed to be date string, else month
	 * @param string|integer (optional) Day of month
	 *
	 * @return array pdate
	 */

	static function set()
	{
		$nargs = func_num_args();
		switch ($nargs) {
		case 0:
			$dt = self::set0();
			break;
		case 2:
			if (empty(func_get_arg(1))) {
				return '';
			}
			$dt = self::set2(func_get_arg(0), func_get_arg(1));
			break;
		case 3:
			$y = func_get_arg(0);
			$m = func_get_arg(1);
			$d = func_get_arg(2);
			if (($m > 12 || $m < 1) || ($d < 1 || $d > 31)) {
				return '';
			}

			$dt = self::set3((int) func_get_arg(0), (int) func_get_arg(1), (int) func_get_arg(2));
			break;
		default:
			// correct way to handle this?
			$dt = self::now();
			break;
		}

		return $dt;
	}

	/************************************************************
	 * DATE MANIPULATION ROUTINES
	 ***********************************************************/

	/**
	 * Adds days (or subtracts) days to date object
     *
     * @param array pdate
	 * @param integer $numdays Number of days to add (or subtract, if negative)
	 *
	 * @return array pdate
	 */

	static function adddays($dt, $numdays)
	{
		$dt['j'] += $numdays;
		$dt = self::normalize($dt, 'j');

		return $dt;
	}

	/**
	 * Add months to date object
     *
     * @param array pdate
	 * @param integer $nummonths Number of months to add (or subtract, if negative)
	 *
	 * @return array pdate
	 */

	static function addmonths($dt, $nummonths)
	{
		$y = $dt['y'];
		$m = $dt['m'];
		$d = $dt['d'];

		$m += $nummonths;
		if ($m <= 0) {
			while ($m <= 0) {
				$m += 12;
				$y--;
			}
		}
		else {
			while ($m > 12) {
				$y++;
				$m -= 12;
			}
		}

		if ($m == 2 and $d > 28) {
			if (self::is_leap_year($y))
				$ndays = 29;
			else 
				$ndays = 28;

			if ($ndays == 29) {
				if ($d != 29) {
					$m++;
					$d -= 29;
				}
			}
			else {
				$m++;
				$d -= 28;
			}
		}
		elseif ($d == 31) {
			if ($m == 4 or $m == 6 or $m == 9 or $m == 11) {
				$d = 1;
				$m++;
				if ($m > 12) {
					$y++;
					$m = 1;
				}
			}
		}

		$dt['y'] = (int) $y;
		$dt['m'] = (int) $m;
		$dt['d'] = (int) $d;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Add years to date object
	 *
     * @param array pdate
	 * @param integer $numyears Number of years to add (or subtract, if negative)
	 *
	 * @return array pdate
	 */

	static function addyears($dt, $numyears)
	{
		$y = $dt['y'];
		$m = $dt['m'];
		$d = $dt['d'];

		$y += $numyears;

		if ($m == 2 and $d > 28) {
			if (self::is_leap_year($y)) {
				$m++;
				$d -= 29;
			}
			else {
				$m++;
				$d -= 28;
			}
		}
		elseif ($d == 31) {
			if ($m == 4 or $m == 6 or $m == 9 or $m == 11) {
				$d = 1;
				$m++;
				if ($m > 12) {
					$y++;
					$m = 1;
				}
			}
		}

		$dt['y'] = (int) $y;
		$dt['m'] = (int) $m;
		$dt['d'] = (int) $d;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Given the date of the date object, return date object revised
	 * so that it is now the date of the beginning of the week (Friday, day 5)
	 *
     * @param array pdate
	 * @param integer $eow (Optional) integer representing the day number (0 = Sunday) for end of week (sic)
	 *
	 * @return array pdate
	 */

	static function begwk($dtobj, $eow = self::weday)
	{
		if (empty($eow))
			$eow = self::weday;
		$d = $dtobj['w'];
		$bow = $eow + 1 - $d;
		if ($bow < 1)
			$bow += 7;
		$bow -= 7;
		$dtobj = self::adddays($dtobj, $bow);

		return $dtobj;
	}

	/**
	 * Given the date of the date object, return date object revised
	 * so that it is now the date of the end of the week (Friday, day 5)
	 *
     * @param array pdate
	 * @param integer $eow (Optional) integer representing the day number (0 = Sunday) for end of week
	 *
	 * @return array pdate
	 */

	static function endwk($dtobj, $eow = self::weday)
	{
		if (empty($eow))
			$eow = self::weday;
		$d = $dtobj['w'];
		$bow = $eow + 1 - $d;
		if ($bow < 1)
			$bow += 7;
		$bow -= 1;
		$dtobj = self::adddays($dtobj, $bow);

		return $dtobj;
	}

	/**
	 * to_iso8601()
	 *
	 * Derive an ISO8601 date string from a date string and return
	 * it.
	 *
	 * @param array $dtobj The pdate you want the ISO date string from
	 * @return string The ISO8601 string from the date object.
	 */

	static function toiso($dtobj)
	{
		$dt_str = sprintf('%d-%02d-%02d', $dtobj['y'], $dtobj['m'], $dtobj['d']);
		return $dt_str;
	}

    /**
     * Return Unix epoch seconds from date.
     *
     * @param array pdate object
     * @return integer Unix epoch seconds
     */

    static function toepoch($dtobj)
    {
        $ret = mktime(12, 0, 0, $dtobj['m'], $dtobj['d'], $dtobj['y']);
        return $ret;
    }

    /**
     * Return the ISO 8601 week number.
     *
     * @param array Date object/array
     * @return integer The ISO week number
     */

    static function isoweek($dtobj)
    {
        $ret = mktime(12, 0, 0, $dtobj['m'], $dtobj['d'], $dtobj['y']);
        $wkno = date('W', $ret);
        return $wkno;
    }

	// The following routines are primarly used for payroll tax form calculations

	/**
	 * Return a date object representing the date of the day before the date's quarter
	 *
	 * @param array $dt pdate
	 *
	 * @return array pdate
	 */

	static function day_before_quarter($dt)
	{
		$qtrs = array(1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4);
		$qtr = $qtrs[$dt['m'] - 1];

		switch ($qtr) {
		case 1:
			$y = $dt['y'] - 1;
			$m = 12;
			$d = 31;
			break;
		case 2:
			$y = $dt['y'];
			$m = 3;
			$d = 31;
			break;
		case 3:
			$y = $dt['y'];
			$m = 6;
			$d = 30;
			break;
		case 4:
			$y = $dt['y'];
			$m = 9;
			$d = 30;
			break;
		}

		$dt['y'] = $y;
		$dt['m'] = $m;
		$dt['d'] = $d;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}

	/**
	 * Return a date object representing the date of the day after the date's quarter
	 *
	 * @param array $dt pdate
	 * @return array pdate
	 */

	static function day_after_quarter($dt)
	{
		$qtrs = array(1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4);
		$qtr = $qtrs[$dt['m'] - 1];
		switch ($qtr) {
		case 1:
			$y = $dt['y'];
			$m = 4;
			$d = 1;
			break;
		case 2:
			$y = $dt['y'];
			$m = 7;
			$d = 1;
			break;
		case 3:
			$y = $dt['y'];
			$m = 10;
			$d = 1;
			break;
		case 4:
			$y = $dt['y'] + 1;
			$m = 1;
			$d = 1;
			break;
		}
		$dt['y'] = $y;
		$dt['m'] = $m;
		$dt['d'] = $d;
		$dt = self::normalize($dt, 'd');

		return $dt;
	}


	/**
	 * Return the date before $this month
     *
     * @param array pdate
	 * @return array pdate
	 */

	static function day_before_month($dt)
	{
		$dt = self::fromints($dt['y'], $dt['m'], 1);
		$dt = self::adddays($dt, -1);

		return $dt;
	}

	/**
	 * Return the date before $this month
     *
     * @param array pdate
	 * @return array pdate
	 */

	static function day_after_month($dt)
	{
		$dt = self::fromints($dt['y'], $dt['m'], $dt['dm']);
		$dt = self::adddays($dt, 1);

		return $dt;
	}

	/**
	 * Return date object representing the day before $this year
	 *
     * @param array pdate
	 * @return array pdate
	 */

	static function day_before_year($dt)
	{
		$d = self::fromints($dt['y'] - 1, 12, 31);
		$dt = self::normalize($d, 'd');

		return $dt;
	}


	/**
	 * Return date object representing the day after $this year
	 *
     * @param array pdate
	 * @return array pdate
	 */

	static function day_after_year($dt)
	{
		$dt['y'] += 1;
		$dt['m'] = 1;
		$dt['d'] = 1;
		$dt = self::normalize($dt);
		return $dt; 
	} 

	/*=============================================================
	 * OUTPUT ROUTINES
	 *============================================================*/

	/**
	 * Output string representing $this date in the given format
	 *
     * @param array pdate
	 * @param string $fmt
	 *
	 * @return string Formatted string derived from $this date, or FALSE on failure.
	 */

	static function get($dt, $fmt)
	{
		if (isset($dt['error']) && $dt['error'] === TRUE) {
			return FALSE;
		}
		if (empty($dt) || $dt === FALSE) {
			return FALSE;
		}
		if (empty($fmt)) {
			return FALSE;
		}

		// day section

		$di = (int) $dt['d'];
		if ($di < 10) {
			$d = '0' . $di;
		}
		else {
			$d = (string) $dt['d'];
		}
		$fmt = str_replace('d', $d, $fmt);

		// month section

		$mi = (int) $dt['m'];
		if ($mi < 10) {
			$m = '0' . $mi;
		}
		else {
			$m = (string) $dt['m'];
		}
		$fmt = str_replace('m', $m, $fmt);

		// year section

		// check for existence of Y and/or y
		$p = 0;
		$py = strpos($fmt, 'Y');
		if ($py !== FALSE) {
			// Y present
			$p += 1;
		}
		$pz = strpos($fmt, 'y');
		if ($pz !== FALSE) {
			// y present
			$p += 2;
		}

		$ps = (string) $dt['y'];
		$p2 = substr($ps, 2, 2);

		switch ($p) {
		case 3:
			// both 2 and 4 digit years present
			$fmt = str_replace('Y', $dt['y'], $fmt);
			$fmt = str_replace('y', $p2, $fmt);
			break;
		case 2:
			// only y present
			$fmt = str_replace('y', $p2, $fmt);
			break;
		case 1:
			// only Y present
			$fmt = str_replace('Y', $dt['y'], $fmt);
			break;
		}

		return $fmt;
	}

	/**
	 * Set a date according to specified format, then return it in
	 * the output format specified.
	 * A sort of one-stop-shop routine sort of.
	 *
	 * @param string $in_format The format of the inbound date
	 * @param string $date Date string
	 * @param string $out_format The format of the output date
	 *
	 * @return string The resulting date, according to the output format, or FALSE on failure
	 */

	static function reformat($in_format, $date, $out_format)
	{
		if (empty($in_format) || empty($date) || empty($out_format))
			return '';
		$indt = self::set($in_format, $date);
		if (!empty($indt))
			$outdt = self::get($indt, $out_format);
		else
			$outdt = '';

		return $outdt;
	}

	/**
	 * Convert American date to ISO8601
	 *
	 * American format date (m/d/y, m-d-y, mdy) to ISO date (Y-m-d)
	 * This is a shortcut for 
	 * date::reformat($date_template,  $american_date, 'Y-m-d'),
	 * which I have to do a lot. This converts from the format entered
	 * into a form to the ISO date (for database).
	 *
	 * @param string $american_date American date from a form
	 *
	 * @return string ISO 8601 format date
	 *
	 */

	static function am2iso($american_date)
	{
		global $date_template;

		if (empty($american_date)) {
			return '';
		}

		$indt = self::set($date_template, $american_date);
		if (!empty($indt))
			$outdt = self::get($indt, 'Y-m-d');
		else
			$outdt = '';

		return $outdt;
	}

	/**
	 * ISO 8601 format date (Y-m-d) to American date (m/d/y)
     *
	 * This is a shortcut for
	 * date::reformat('Y-m-d', $iso_date, 'm/d/y'),
	 * which I have to do a lot. This converts from the format in
	 * the database to an American date format.
	 *
	 * @param string $iso_date Date in ISO 8601 (CCYYY-MM-DD) format
	 *
	 * @return string American format date
	 *
	 */

	static function iso2am($iso_date)
	{
		if (empty($iso_date)) {
			return '';
		}

		$indt = self::set('Y-m-d', $iso_date);
		if (!empty($indt))
			$outdt = self::get($indt, 'm/d/y');
		else
			$outdt = '';

		return $outdt;
	}

    /**
     * Output today's date in ISO8601 format
     *
     * @return string ISO formatted date
     */

	static function now2iso()
	{
		$dt = self::now();
		return sprintf('%d-%02d-%02d', $dt['y'], $dt['m'], $dt['d']);
	}

    /**
     * Output today's date in American (m/d/y) format
     *
     * @return string American formatted date
     */

	static function now2am()
	{
		$dt = self::now();
		return sprintf('%02d/%02d/%02d', $dt['m'], $dt['d'], substr($dt['y'], 2));
	}

	/************************************************************
	 * INTROSPECTION METHODS
	 ***********************************************************/

	/**
	 * (Mostly) Internal routine for comparisons
	 * If passed date is later than this date, return negative difference
	 * If passed date is the same as this date, return 0
	 * If passed date is earlier than this date, return positive difference
	 *
	 * @param array pdate
     * @param array pdate
	 *
	 * @return integer < 0 if $dt > $this, 0 if $dt == $this, > 0 if $dt < $this
	 */

	static function compare($dt1, $dt2)
	{
		// It's simplest to make this comparison using the jdays,
		// but they're floats. Thus errors in computation can result.
		// Jdays, because of the math, always end in ".5" (if we don't
		// consider time of day).
		//
		// PHP docs say that, by default, rounding occurs as we were
		// taught, with .5 rounding up always. This routine assumes
		// PHP will keep that promise.

		return round($dt1['j']) - round($dt2['j']);
	}

	/**
	 * If this date is later than the passed date, return true, else false
     *
     * @param array pdate
	 * @param array pdate
	 *
	 * @return boolean True if $this > $dt, else false
	 */

	static function later_than($dt1, $dt2)
	{
		return (self::compare($dt1, $dt2) > 0) ? true : false;
	}

	/**
	 * Synonym for later_than().
	 */

	static function after($dt1, $dt2)
	{
		return self::later_than($dt1, $dt2);
	}

	/**
	 * If this date is earlier than the passed date, return true, else false
     *
     * @param array pdate
	 * @param array pdate
	 *
	 * @return boolean True if $this < $dt, else false
	 */

	static function earlier_than($dt1, $dt2)
	{
		return (self::compare($dt1, $dt2) < 0) ? true : false;
	}

	/**
	 * Synonym for the earlier_than()
	 */

	static function before($dt1, $dt2)
	{
		return self::earlier_than($dt1, $dt2);
	}

	/**
	 * If this date is the same as the passed date, return true, else false
	 *
     * @param array pdate
	 * @param array pdate
	 *
	 * @return boolean True if $this == $dt, else false
	 */

	static function same_as($dt1, $dt2)
	{
		return (self::compare($dt1, $dt2) == 0) ? true : false;
	}

	/**
	 * The number of days between the supplied date and today.
	 * If today is before the supplied date, the number is negative.
	 * If today is after the supplied date, the number is positive.
	 *
     * @param array pdate
	 * @param array pdate
	 *
	 * @return integer The number of days between the two dates.
	 */

	static function days_diff($dt1, $dt2)
	{
		return self::compare($dt1, $dt2);
	}

	/**
     * Dump the contents of a pdate array
     *
	 * Just for debugging purposes...
     *
     * @param array pdate
	 */

	static function dump($dt)
	{
		print "Julian Day = " . $dt['j'] . "<br>\n";

		$m1 = (string) $dt['m'];
		$ms = str_pad($m1, 2, '0', STR_PAD_LEFT); 

		$d1 = (string) $dt['d'];
		$ds = str_pad($d1, 2, '0', STR_PAD_LEFT);

		print "Year-Month-Day = " . $dt['y'] . '-' . $ms . '-' . $ds . "<br>\n";
		print "Day Of Week = " . $dt['w'] . '<br/>' . PHP_EOL;
		print 'Day Of Year = ' . $dt['dy'] . '<br/>' . PHP_EOL;
		print 'Week Of Year = ' . $dt['wy'] . '<br/>' . PHP_EOL;
		print 'Days In Month = ' . $dt['dm'] . '<br/>' . PHP_EOL;
	}

	static public function version()
	{
		return 9.0;
	}

};


