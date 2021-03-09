<?php

include 'init.php';
$pay = model('payee');

$payee_id = $_POST['payee_id'] ?? NULL;
if (is_null($payee_id)) {
	redirect('index.php');
}
$payee = $pay->get_payee($payee_id);

$fields = array(
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'hidden',
		'value' => $payee_id
	),
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Update'
	)
);

$form->set($fields);

view('Edit Payee', [], 'payedt3.php', 'payedt2', 'name');


