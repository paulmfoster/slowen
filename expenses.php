<?php

include 'init.php';

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
		'type' => 'date',
		'value' => $ifrom_date
	),
	'to_date' => array(
		'name' => 'to_date',
		'type' => 'date',
		'value' => $ito_date
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Calculate'
	)
);

$form->set($fields);

view('Weekly Expenses', [], 'expenses2.php', 'expenses');

