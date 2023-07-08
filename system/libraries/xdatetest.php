<?php

include 'xdate.lib.php';

$dt = new xdate();
$dt->dump();

$yn = $dt->is_leap_year();
print "is_leap_year: ";
print ($yn ? 'Yes' : 'No') . '<br/>';

$dm = $dt->days_in_month();
print "days_in_month: " . $dm . '<br/>';

$jd = $dt->jday();
print "jday: " . $jd . '<br/>';

$dt->from_jday($jd);
print "from_jday: ";
$dt->dump();

$dw = $dt->day_of_week();
print "day_of_week: " . $dw . '<br/>';

$dy = $dt->day_of_year();
print "day_of_year: " . $dy . '<br/>';

$wy = $dt->week_of_year();
print "week_of_year: " . $wy . '<br/>';

$ad = $dt->add_days(4);
print "add_days(4): ";
$dt->dump();

$dt->today();
$am = $dt->add_months(2);
print "add_months(2): ";
$dt->dump();

$dt->today();
$am = $dt->add_years(3);
print "add_years(3): ";
$dt->dump();

$dt->today();
$bw = $dt->begwk();
print "begwk: ";
$dt->dump();

$dt->today();
$bw = $dt->endwk();
print "endwk: ";
$dt->dump();

$dt->today();
$iso = $dt->to_iso();
print "to_iso: " . $iso . '<br/>';

$dt->from_iso($iso);
print "from_iso: ";
$dt->dump();

$i2a = $dt->iso2amer($iso);
print "iso2amer: " . $i2a . '<br/>';

$te = $dt->to_epoch();
print "to_epoch: " . $te . '<br/>';

$dt->from_epoch($te);
print "from_epoch: ";
$dt->dump();

$dbq = $dt->day_before_quarter();
print "day_before_quarter: ";
$dt->dump();

$dt->today();
$daq = $dt->day_after_quarter();
print "day_after_quarter: ";
$dt->dump();

$dt->today();
$dbm = $dt->day_before_month();
print "day_before_month: ";
$dt->dump();

$dt->today();
$dbm = $dt->day_after_month();
print "day_after_month: ";
$dt->dump();

$dt->today();
$dby = $dt->day_before_year();
print "day_before_year: ";
$dt->dump();

$dt->today();
$dby = $dt->day_after_year();
print "day_after_year: ";
$dt->dump();

$dt->today();
$eom = $dt->end_of_month();
print "end_of_month: ";
$dt->dump();

$dt->today();
$dt->from_ints($dt->year, $dt->month, $dt->day);
print "from_ints: ";
$dt->dump();

$dt->from_amer('06/15/2023');
print "from_amer(06/15/2023): ";
$dt->dump();

$dt->today();
$a2i = $dt->amer2iso('06/15/2023');
print "amer2iso(06/15/2023): " . $a2i . '<br/>';

$dt->today();
$comp = new xdate();
$comp->from_ints(2024, 5, 22);

$tf = $dt->before($comp);
print "before(2024-05-22): ";
print ($tf) ? 'TRUE' : 'FALSE';
print '<br/>';

$tf = $dt->after($comp);
print "after(2024-05-22): ";
print ($tf) ? 'TRUE' : 'FALSE';
print '<br/>';

$tf = $dt->same($comp);
print "same(2024-05-22): ";
print ($tf) ? 'TRUE' : 'FALSE';
print '<br/>';

