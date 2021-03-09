<?php

include 'init.php';
$pay = model('payee');

$payee_id = $_POST['payee_id'] ?? NULL;
if (is_null($payee_id)) {
	redirect('paydel.php');
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

view('Delete Payee', ['payee' => $payee], 'paydel3.php', 'paydel2', 'name');

