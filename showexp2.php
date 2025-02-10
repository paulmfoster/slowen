<?php

include 'init.php';

$report = model('report', $db);

$expenses = $report->get_expenses($_POST['from_date'], $_POST['to_date']);
$page_title = 'Weekly Expenses';
include VIEWDIR . 'showexp2.view.php';


