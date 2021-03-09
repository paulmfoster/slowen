<?php

include 'init.php';
$rpt = model('report');

$payees = $rpt->get_payees();

$payee_options = array();
foreach ($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$fields = array(
	'payee' => array(
		'name' => 'payee',
		'type' => 'select',
		'options' => $payee_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Search'
	)
);
$form->set($fields);

view('Search Payees', [], 'results.php', 'srchpays');

