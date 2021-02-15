<?php

include 'init.php';
$pay = load_model('payee');

$payee_id = $_POST['payee_id'] ?? NULL;
if (is_null($payee_id)) {
	relocate('paydel.php');
}
$payee = $pay->get_payee($payee_id);

$fields = array(
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'hidden',
		'value' => $payee_id
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Delete'
	)
);

$form->set($fields);

$focus_field = 'name';
$page_title = 'Delete Payee';
$view_file = view_file('paydel2');
include 'view.php';
