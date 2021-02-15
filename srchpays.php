<?php

include 'init.php';
$rpt = load_model('report');

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
	's2' => array(
		'name' => 's2',
		'type' => 'submit',
		'value' => 'Search'
	)
);
$form->set($fields);

$page_title = 'Search';
$view_file = view_file('srchpays');
include 'view.php';

