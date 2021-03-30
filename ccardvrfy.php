<?php

// user has entered a credit card charge; now user must verify it

include 'init.php';
memory::merge($_POST);
memory::set('amount', - $_POST['dr_amount']);
$trans = model('addtxn');

if ($cfg['confirm_transactions'] == 0) {
	$txnid = $trans->add_transaction(memory::get_all());
	memory::clear();
	redirect('ccardadd.php');
}

$fields = array(
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);
$form->set($fields);

$data = memory::get_all();
$names = $trans->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];

view('Confirm Credit Card Charge', ['data' => $data], 'txnsave.php', 'ccardvrfy');

