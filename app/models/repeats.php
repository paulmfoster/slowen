<?php

// assumes xdate.lib.php present

class repeats
{
    public $dates[];

    function get()
    {
        return $this->dates;
    }

    /**
     * Generate a date which is the $occ $dow of a month.
     *
     * Given the year and month, find a date which is the proper $dow, but the
     * $occ occurrence of it.
     *
     * @param int year
     * @param int month
     * @param int day of the week (0-6)
     * @param int occurrence in month (1-5 5=last)
     * @return xdate resulting date
     */

    function nth_dow($year, $month, $dow, $occ)
    {
        $dt = new xdate();
        $dt->from_ints($year, $month, 1);

        if ($occ == 5) {
            $dt->end_of_month();
            while ($dt->day_of_week() != $dow) {
                $dt->add_days(-1);
            }
        }
        elseif ($occ == 1) {
            while ($dt->day_of_week() != $dow)
                $dt->add_days(1);
        }
        elseif ($occ < 5) {
            while ($dt->day_of_week() != $dow)
                $dt->add_days(1);

            $dt->add_days(($occ - 1) * 7);
        }

        return $dt;
    }

    /**
     * Repeat by the day.
     *
     * @param array xdate array of the last occurrence date
     * @param int frequency of the repetition
     * @param long julian day of first day of month
     * @param long julian day of the last day of the month
     * @return array of xdates of repetitions
     */

    function day_repeats($last, $freq, $jfrom, $jto)
    {
        $dt = clone $last;

        if ($freq == 0)
            $freq = 1;

        while ($dt->jday() < $jfrom) 
            $dt->add_days($freq);

        do {
            if ($dt->jday() <= $jto) {
                $x = clone $dt;
                $this->dates[] = $x;
            }
            $dt->add_days($freq);
        } while ($dt->jday() <= $jto);
    }

    /**
     * Repeat by the week.
     *
     * Repeat every $freq weeks from $last.
     *
     * 2024-10-06:PW:every week on Sunday
     * 2024-10-06:PWF2:every other week on Sunday
     *
     * @param array xdate array of the last occurrence date
     * @param int frequency of the repetition
     * @param long julian day of first day of month
     * @param long julian day of the last day of the month
     * @return array of xdates of repetitions
     */

    function week_repeats($last, $freq, $jfrom, $jto)
    {
        $dt = new xdate;

        if ($freq == 0)
            $freq = 1;

        // rather than iterate from $last to $jfrom (could be years), figure out how many
        // iterations to get there, and do it all at once
        $jlast = $last->jday();
        $days_diff = $jfrom - $jlast;
        $min_iters = floor($days_diff / ($freq * 7));

        $dt = clone $last;
        $dt->add_days($min_iters * ($freq * 7));
        while ($dt->jday() < $jfrom)
            $dt->add_days($freq * 7);

        do {
            if ($dt->jday() <= $jto) {
                $x = clone $dt;
                $this->dates[] = $x;
            }
            $dt->add_days($freq * 7);
        } while ($dt->jday() <= $jto);
    }

    /**
     * Repeat by month.
     *
     * Repeat spec: P is the period (days, weeks, months, etc.)
     * PM = repeat by month
     * F = frequency
     * PMF2 = repeat every 2 months
     * S = occurrence in month (day of the week)
     * S1 to S4 = 1st to 4th X-day of the month
     * (X-day is determined by the day of week of the $last variable)
     * S5 = last X-day of the month
     * S6 = *all* X-days of the month
     *
     * NOTE: freq = 0 is the same as freq = 1
     *
     * Examples:
     *
     * 2024-10-06:PM:every month on the 5th of the month (10/5/24 is Sunday)
     * 2024-10-06:PMF1:every month on the 5th of the month (== F0)
     * 2024-10-06:PMF2:every second month on the 5th of the month
     * 2024-10-06:PMS1:every month, 1st Sunday of the month
     * 2024-10-06:PMF2S3:every other month, 3rd Sat of the month
     * 2024-10-26:PMS4:every month, 4th Sunday of the month
     * 2024-10-26:PMS5:every month, last Sunday of the month
     * 2024-10-26:PMS6:every month, all Sunday in the month
     *
     * @param array The xdate array of the last occurrence
     * @param int frequency of the repetition, in months
     * @param int which occurrence of that day of the week in the month
     * @param long julian day of the beginning of this month
     * @param long julian day of the end of the month
     * @return array of xdates satisfying the repetition spec
     */

    function month_repeats($last, $freq, $occ, $jfrom, $jto)
    {
        $dow = $last->day_of_week();
        $dt = clone $last;

        if ($freq == 0)
            $freq = 1;

        while ($dt->jday() < $jfrom)
            $dt->add_months($freq);

        if ($occ == 0) {
            do {
                if ($dt->jday() <= $jto) {
                    $x = clone $dt;
                    $this->dates[] = $x;
                }
                $dt->add_months($freq);
            } while ($dt->jday() <= $jto);
        }
        else {
            $dt->day = 1;
            while ($dt->day_of_week() != $dow)
                $dt->add_days(1);
            if ($occ > 0 && $occ < 6) {
                $x = $this->nth_dow($dt->year, $dt->month, $dow, $occ);
                if ($x->jday() <= $jto)
                    $this->dates[] = $x;
            }
            elseif ($occ == 6) {
                while ($dt->jday() <= $jto) {
                    $x = clone $dt;
                    $this->dates[] = $x;
                    $dt->add_days(7);
                }
            }
        }
    }

    /**
     * Repeat an event quarterly.
     *
     * Repetition happens on the same day of the month as in $last.
     *
     * 2024-10-06:PQ:Repeat every quarter.
     * 2024-10-06:PQF2:Repeat every other quarter
     *
     * @param array The xdate array of the last occurrence
     * @param int frequency of the repetition, in months
     * @param long julian day of the beginning of this month
     * @param long julian day of the end of the month
     * @return array of xdates satisfying the repetition spec
     */

    function quarter_repeats($last, $freq, $jfrom, $jto)
    {
        $dt = clone $last;

        if ($freq == 0)
            $freq = 1;

        while ($dt->jday() < $jfrom) 
            $dt->add_months($freq * 3);

        do {
            if ($dt->jday() <= $jto) {
                $x = clone $dt;
                $this->dates[] = $x;
            }
            $dt->add_months($freq * 3);
        } while ($dt->jday() <= $jto);
    }

    /**
     * Repeat every year.
     *
     * @param array The xdate array of the last occurrence
     * @param int frequency of the repetition, in months
     * @param long julian day of the beginning of this month
     * @param long julian day of the end of the month
     * @return array of xdates satisfying the repetition spec
     */

    function year_repeats($last, $freq, $jfrom, $jto)
    {
        $dt = clone $last;

        if ($freq == 0)
            $freq = 1;

        while ($dt->jday() < $jfrom) 
            $dt->add_years($freq);

        do {
            if ($dt->jday() <= $jto) {
                $x = clone $dt;
                $this->dates[] = $x;
            }
            $dt->add_years($freq);
        } while ($dt->jday() <= $jto);
    }

    /**
     * Repeat an occurrence.
     *
     * @param xdate of last occurrence
     * @param string repeat spec
     * @param xdate of beginning of interval
     * @param xdate of end of interval
     * @return array of xdates of repetitions
     */

    function next($last, $period, $frequency, $occurrence, $from, $to)
    {
        $jfrom = $from->jday();
        $jto = $to->jday();
        $dates = [];

        switch ($period) {
        case 'D':
            $dates = day_repeats($last, $frequency, $jfrom, $jto);
            break;
        case 'W':
            $dates = week_repeats($last, $frequency, $jfrom, $jto);
            break;
        case 'M':
            $dates = month_repeats($last, $frequency, $occurrence, $jfrom, $jto);
            break;
        case 'Q':
            $dates = quarter_repeats($last, $frequency, $jfrom, $jto);
            break;
        case 'Y':
            $dates = year_repeats($last, $frequency, $jfrom, $jto);
            break;
        }

        return $dates;
    }
}


// /*
//  * 2024-10-06:PD:every day
//  * 2024-10-06:PDF3:every 3rd day (from the original date)
//  *
//  * 2024-10-06:PW:every week on Saturday (10/5/24 is Saturday)
//  * 2024-10-06:PWF2:every 2 weeks on Saturday
//  *
//  * 2024-10-06:PM:every month on the 5th of the month (10/5/24 is Saturday)
//  * 2024-10-06:PMF2:every second month on the 5th of the month
//  * 2024-10-06:PMS1:every month, 1st Saturday of the month
//  * 2024-10-06:PMF2S1:every other month, 3rd Sat of the month
//  * 2024-10-26:PMS4:every month, 4th Saturday of the month
//  * 2024-10-26:PMS5:every month, last Saturday of the month
//  * 2024-10-26:PMS6:every month, all Saturdays in the month
//  *
//  * 2024-10-06:PQ:every quarter on the 6th of the 1st month of the quarter
//  * 2024-10-06:PQF2:every 2nd quarter on the 6th of the 1st month of quarter
//  *
//  * 2024-10-06:PY:every year on the 6th of October
//  * 2024-10-06:PYF2:every 2nd year on the 6th of October
//  */
//
//
// function doit($dt, $period, $freq, $occ, $from, $to)
// {
//     $dates = repeats($dt, $period, $freq, $occ, $from, $to);
//     $ifrom = $from->to_iso();
//     $ito = $to->to_iso();
//     $idt = $dt->to_iso();
//
//     if (count($dates) != 0) {
//         foreach ($dates as $newdt) {
//             // print_r($dates);
//             $isodate = $newdt->to_iso();
//             echo "last: $idt, date: $isodate, per: $period, freq: $freq, occ: $occ, from: $ifrom, to: $ito<br/>";
//         }
//     }
// }
//
// $dfrom = new xdate;
// $dfrom->from_ints(2024, 12, 1);
//
// $dto = new xdate;
// $dto->from_ints(2024, 12, 31);
//
// $dt = new xdate;
// $dt->from_ints(2024, 10, 9);
//
// echo "<h2>Daily</h2>";
// doit($dt, 'D', 0, 0, $dfrom, $dto);
// doit($dt, 'D', 2, 0, $dfrom, $dto);
//
// echo "<h2>Weekly</h2>";
// doit($dt, 'W', 0, 0, $dfrom, $dto);
// doit($dt, 'W', 1, 0, $dfrom, $dto);
// doit($dt, 'W', 2, 0, $dfrom, $dto);
// doit($dt, 'W', 3, 0, $dfrom, $dto);
// doit($dt, 'W', 4, 0, $dfrom, $dto);
// doit($dt, 'W', 5, 0, $dfrom, $dto);
//
// echo "<h2>Monthly</h2>";
// doit($dt, 'M', 0, 0, $dfrom, $dto);
// doit($dt, 'M', 2, 0, $dfrom, $dto);
// doit($dt, 'M', 3, 0, $dfrom, $dto);
//
// doit($dt, 'M', 0, 1, $dfrom, $dto);
// doit($dt, 'M', 0, 2, $dfrom, $dto);
// doit($dt, 'M', 0, 3, $dfrom, $dto);
// doit($dt, 'M', 0, 4, $dfrom, $dto);
// doit($dt, 'M', 0, 5, $dfrom, $dto);
// doit($dt, 'M', 0, 6, $dfrom, $dto);
//
// doit($dt, 'M', 1, 2, $dfrom, $dto);
// doit($dt, 'M', 2, 3, $dfrom, $dto);
//
// // these should produce no results
// doit($dt, 'M', 3, 4, $dfrom, $dto);
// doit($dt, 'M', 4, 5, $dfrom, $dto);
// doit($dt, 'M', 5, 6, $dfrom, $dto);
//
// echo "<h2>Quarterly (1 year)</h2>";
//
// $x = clone $dto;
// $x->add_days(365); // 1 year
//
// doit($dt, 'Q', 0, 0, $dfrom, $x);
// doit($dt, 'Q', 2, 0, $dfrom, $x);
//
// echo "<h2>Yearly (5 years)</h2>";
//
// $x = clone $dto;
// $x->add_days(1825); // 5 years
//
// doit($dt, 'Y', 0, 0, $dfrom, $x);
// doit($dt, 'Y', 2, 0, $dfrom, $x);
//
