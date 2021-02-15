<?php

include 'init.php';
$rpt = load_model('report');

$expenses = $rpt->get_expenses($_POST['from_date'], $_POST['to_date']);

$view_file = view_file('expenses2');
$page_title = 'Weekly Expenses';
include 'view.php';

