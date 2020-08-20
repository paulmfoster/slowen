<?php

include 'init.php';

if (empty($_POST)) {

	$stage = 1;

	$temp_date = pdate::now();

	$oto_date = pdate::endwk($temp_date);
	$ato_date = pdate::get($oto_date, 'm/d/y');
	$ito_date = pdate::get($oto_date, 'Y-m-d');

	$ofrom_date = pdate::adddays($oto_date, -6);
	$afrom_date = pdate::get($ofrom_date, 'm/d/y');
	$ifrom_date = pdate::get($ofrom_date, 'Y-m-d');

	$fields = array(
		'from_date' => array(
			'name' => 'from_date',
			'type' => 'date'
		),
		'to_date' => array(
			'name' => 'to_date',
			'type' => 'date'
		),
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Calculate'
		)
	);

	$form = new form($fields);
}
else {
	$stage = 2;
	$expenses = $sm->get_expenses($_POST['from_date'], $_POST['to_date']);
}

$view_file = 'views/expenses.view.php';
$page_title = 'Weekly Expenses';
include 'view.php';

