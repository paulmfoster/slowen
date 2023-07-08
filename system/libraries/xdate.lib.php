<?php

/**
 * @copyright  2023 Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 9.0
 */

/**
 * xdate class
 *
 * THIS CLASS DOES NOT DEAL WITH TIMES, JUST DATES.
 *
 * This date class handles a variety of things that PHP date classes don't.
 * It uses julian dates to calculate dates and only uses native PHP date functions
 * to instantiate date objects when no other parameters are given.
 *
 * Native class members:
 *
 * year
 * month
 * day
 *
 * This class is a refactor and simplification of the pdate class.
 *
 */

class xdate 
{

	// Week ending day
	const weday = 6; // Saturday
    var $year, $month, $day;

    function __construct()
    {
        $this->today();
    }

	/**
	 * Return today's date as pdate object/array.
     *
     * @return array pdate
	 */

	function today()
	{
		$now = getdate(time());
		$this->year = $now['year'];
		$this->month = $now['mon'];
		$this->day = $now['mday'];
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
	 * @return boolean True if leap year, false otherwise
	 */

	function is_leap_year()
	{
		if (($this->year % 4 == 0) && (($this->year % 100 != 0) || ($this->year % 400 == 0)))
			return true;
		else
			return false;
	}

	/**
	 * Return days in the month for a month/year
	 *
	 * @return integer Days in the month
	 */

	function days_in_month()
	{
		$mdays = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if ($this->month == 2) {
			if ($this->is_leap_year($this->year))
				return 29;
			else
				return 28;
		}
		else
			return $mdays[$this->month - 1];
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
	
	function jday()
	{
	    if ($this->month < 3) {
	        $m = $this->month + 12;
	        $y = $this->year - 1;
	    }
	    else {
	        $m = $this->month;
	        $y = $this->year;
	    }
	    $c = 2 - floor($y / 100) + floor($y / 400);
	    $jday = floor(1461 * ($y + 4716) / 4) + floor(153 * ($m + 1) / 5) + $this->day + $c - 1524.5;
	    return $jday;
	}
	
	/**
	 * Determines day of week for julian day
	 * Internal function
	 *
	 * @return integer Day of week, 0 = Sunday
	 */

	function day_of_week()
	{
        $jday = $this->jday();
		$n = (int) ($jday + 0.5) + 1;

		return $n % 7;
	}

    function day_of_year()
    {
        $dt = new xdate();
        $c = 0;
        for ($i = 1; $i < $this->month; $i++) {
            $dt->from_ints($this->year, $i, 1);
            $dim = $dt->days_in_month();
            $c += $dim;
        }
        $c += $this->day;

        return $c;
    }

    function week_of_year()
    {
        $dow = $this->day_of_week();
        $doy = $this->day_of_year();

        // our week starts Sun = 0
        // ISO week starts Mon = 1, Sun = 7
        $dow++;
        $n = floor(($doy - $dow + 10) / 7);
        return $n;
    }

	/**
	 * Adds days (or subtracts) days to date object
     *
	 * @param integer $numdays Number of days to add (or subtract, if negative)
	 */

	function add_days($numdays)
	{
        $jday = $this->jday();
		$jday+= $numdays;
        $this->from_jday($jday);
	}

	/**
	 * Add months to date object
     *
	 * @param integer $nummonths Number of months to add (or subtract, if negative)
	 */

	function add_months($nummonths)
	{
		$y = $this->year;
		$m = $this->month;
		$d = $this->day;

		$this->month += $nummonths;
		if ($this->month <= 0) {
			while ($this->month <= 0) {
				$this->month += 12;
				$this->year--;
			}
		}
		else {
			while ($this->month > 12) {
				$this->year++;
				$this->month -= 12;
			}
		}

		if ($this->month == 2 and $this->day > 28) {
			if ($this->is_leap_year())
				$ndays = 29;
			else 
				$ndays = 28;

			if ($ndays == 29) {
				if ($this->day != 29) {
					$this->month++;
					$this->day -= 29;
				}
			}
			else {
				$this->month++;
				$this->day -= 28;
			}
		}
		elseif ($this->day == 31) {
			if ($this->month == 4 or $this->month == 6 or $this->month == 9 or $this->month == 11) {
				$this->day = 1;
				$this->month++;
				if ($this->month > 12) {
					$this->year++;
					$this->month = 1;
				}
			}
		}
	}

	/**
	 * Add years to date
	 *
	 * @param integer $numyears Number of years to add (or subtract, if negative)
	 */

	function add_years($numyears)
	{
		$this->year += $numyears;

		if ($this->month == 2 and $this->day > 28) {
			if ($this->is_leap_year()) {
				$this->month++;
				$this->day -= 29;
			}
			else {
				$this->month++;
				$this->day -= 28;
			}
		}
		elseif ($this->day == 31) {
			if ($this->month == 4 or $this->month == 6 or $this->month == 9 or $this->month == 11) {
				$this->day = 1;
				$this->month++;
				if ($this->month > 12) {
					$this->year++;
					$this->month = 1;
				}
			}
		}
	}

	/**
	 * Given the date of the date object, return date object revised
	 * so that it is now the date of the beginning of the week (Friday, day 5)
	 *
	 * @param integer $eow (Optional) integer representing the day number (0 = Sunday) for end of week (sic)
	 */

	function begwk($eow = self::weday)
	{
        $d = $this->day_of_week();
        $bow = $eow + 1 - $d;
		if ($bow < 1)
			$bow += 7;
		$bow -= 7;
        $this->add_days($bow);
	}

	/**
	 * Given the date of the date object, return date object revised
	 * so that it is now the date of the end of the week (Friday, day 5)
	 *
	 * @param integer $eow (Optional) integer representing the day number (0 = Sunday) for end of week
	 */

	function endwk($eow = self::weday)
	{
		$d = $this->day_of_week();
		$bow = $eow + 1 - $d;
		if ($bow < 1)
			$bow += 7;
		$bow -= 1;
        $this->add_days($bow);
	}

	/**
	 * Output ISO date.
	 *
	 * Derive an ISO8601 date string from a date string and return
	 * it.
	 *
	 * @return string The ISO8601 string from the date object.
	 */

	function to_iso()
	{
		$dt_str = sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
		return $dt_str;
	}

    function to_amer()
    {
        $dt_str = sprintf('%02d/%02d/%04d', $this->month, $this->day, $this->year);
        return $dt_str;
    }

    /**
     * Return Unix epoch seconds from date.
     *
     * @return integer Unix epoch seconds
     */

    function to_epoch()
    {
        $ret = mktime(12, 0, 0, $this->month, $this->day, $this->year);
        return $ret;
    }

	// The following routines are primarly used for payroll tax form calculations

	/**
	 * Set the date object representing the date of the day before the date's quarter
	 */

	function day_before_quarter()
	{
		$qtrs = array(1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4);
		$qtr = $qtrs[$this->month - 1];

		switch ($qtr) {
		case 1:
			$this->year--;
			$this->month = 12;
			$this->day = 31;
			break;
		case 2:
			$this->month = 3;
			$this->day = 31;
			break;
		case 3:
			$this->month = 6;
			$this->day = 30;
			break;
		case 4:
			$this->month = 9;
			$this->day = 30;
			break;
		}
	}

	/**
	 * Set the date object representing the date of the day after the date's quarter
	 */

	function day_after_quarter()
	{
		$qtrs = array(1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4);
		$qtr = $qtrs[$this->month - 1];
		switch ($qtr) {
		case 1:
			$this->month = 4;
			$this->day = 1;
			break;
		case 2:
			$this->month = 7;
			$this->day = 1;
			break;
		case 3:
			$this->month = 10;
			$this->day = 1;
			break;
		case 4:
			$this->year++;
			$this->month = 1;
			$this->day = 1;
			break;
		}
	}

	/**
	 * Set the date before $this month
	 */

	function day_before_month()
	{
        $this->from_ints($this->year, $this->month, 1);
        $this->add_days(-1);
	}

	/**
	 * Set the date before $this month
	 */

	function day_after_month()
	{
        $this->from_ints($this->year, $this->month, $this->days_in_month());
        $this->add_days(1);
	}

	/**
	 * Set the date to the day before $this year
	 */

	function day_before_year()
	{
        $this->from_ints($this->year - 1, 12, 31);
	}

	/**
	 * Set the date to the day after $this year
	 */

	function day_after_year()
	{
        $this->from_ints($this->year + 1, 1, 1);
	} 

    function end_of_month()
    {
        $this->day = $this->days_in_month();
    }

    function from_jday($jday)
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

        $this->year = $y;
        $this->month = $m;
        $this->day = $d;
    }

	/**
	 * Return pdate object from year, month and day
	 *
	 * @param integer $yr Year
	 * @param integer $mo Month
	 * @param integer $da Day of month
	 *
	 * @return array xdate
	 */

	function from_ints($yr, $mo, $da) 
	{
		if ($yr < 100) {
			// POSIX X/Open standard "window"
			if ($yr >= 69 and $yr <= 99)
				$yr = 1900 + $yr;
			else
				$yr = 2000 + $yr;
		}

		$this->year = $yr;
		$this->month = $mo;
		$this->day = $da;
	}

	/**
	 * Return pdate based on ISO 8601 date (ccyy-mm-dd)
	 *
	 * @param string $iso The ISO 8601 date
	 */

	function from_iso($iso)
	{
        list($dty, $dtm, $dtd) = explode('-', $iso);
        $this->year = (int) $dty;
        $this->month = (int) $dtm;
        $this->day = (int) $dtd;
	}

    /**
     * Set date based on American date string.
     *
     * American date is like: "mm/dd/yyyy"
     *
     * @param string American date string
     */

    function from_amer($amer)
    {
        list($dtm, $dtd, $dty) = explode('/', $amer);
        $this->year = (int) $dty;
        $this->month = (int) $dtm;
        $this->day = (int) $dtd;
    }

    /**
     * Return pdate object from Unix epoch seconds.
     *
     * @param integer Unix epoch seconds
     */

    function from_epoch($secs)
    {
        $d1 = date('Y-m-d', $secs);
        $d2 = explode('-', $d1);
        $this->year = (int) $d2[0];
        $this->month = (int) $d2[1];
        $this->day = (int) $d2[2];
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
     * @return string ISO date
	 */

	function amer2iso($american_date)
	{
        $this->from_amer($american_date);
        return $this->to_iso();
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

	function iso2amer($iso_date)
	{
        $this->from_iso($iso_date);
        return $this->to_amer();
	}

	/**
     * Is the passed xdate after the current one?
     *
     * @param xdate date to compare to
     * @return bool
	 */

	function after($dt2)
	{
        $j1 = $this->jday();
        $j2 = $dt2->jday();
        return ((round($j1) - round($j2)) > 0) ? true : false;
	}

	/**
     * Is the passed xdate before the current one?
     *
     * @param xdate date to compare to
     * @return bool
	 */

	function before($dt2)
	{
        $j1 = $this->jday();
        $j2 = $dt2->jday();
        return ((round($j1) - round($j2)) < 0) ? true : false;
	}

	/**
     * Is the passed xdate the same as the current one?
     *
     * @param xdate date to compare to
     * @return bool
	 */

	function same($dt2)
	{
        if ($year == $dt2->year && $month == $dt2->month && $day == $dt2->day)
            return TRUE;
        else
            return FALSE;
	}

	/**
     * Dump the contents of a pdate array
     *
	 * Just for debugging purposes...
     *
     * @param array pdate
	 */

    function dump()
    {
        printf("%04d-%02d-%02d<br/>", $this->year, $this->month, $this->day);
    }

};

