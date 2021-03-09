<?php

// delete a payee; select which one first

include 'init.php';
$pay = model('payee');

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
		'value' => 'Delete Payee'
	)
);
$form->set($fields);

view('Delete Payee: Select Payee', [], 'paydel2.php', 'payees', 'payee_id');

