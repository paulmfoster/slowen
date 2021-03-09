<?php

// the user has specified the criteria for the expenses report

include 'init.php';
$rpt = model('report');

$expenses = $rpt->get_expenses($_POST['from_date'], $_POST['to_date']);

view('Weekly Expenses', ['expenses' => $expenses], '', 'expenses2');

