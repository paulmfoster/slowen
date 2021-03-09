<?php

include 'init.php';

for ($i = 2016; $i < 2050; $i++) {
	$year_options[] = array('lbl' => $i, 'val' => $i);
}

// $state == 0
$fields = array(
	'year' => array(
		'name' => 'year',
		'type' => 'select',
		'options' => $year_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Report'
	)
);

$form->set($fields);

view('Yearly Audit', [], 'audit2.php', 'audity', 'year');

