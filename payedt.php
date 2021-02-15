<?php

include 'init.php';
$pay = load_model('payee');

$payees = $pay->get_payees();

$id_options = array();
foreach ($payees as $payee) {
	$id_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$fields = array(
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'select',
		'options' => $id_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Edit Payee'
	)
);
$form->set($fields);

$focus_field = 'payee_id';
$page_title = 'Edit Payee: Select Payee';
$view_file = view_file('payees');
$destination = 'payedt2.php';
include 'view.php';

