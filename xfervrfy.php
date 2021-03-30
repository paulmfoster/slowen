<?php

// user has entered xfer transaction; now verify it

include 'init.php';
$trans = model('addtxn');
memory::merge($_POST);
memory::set('amount', - $_POST['dr_amount']);

if ($cfg['confirm_transactions'] == 0) {
	$txnid = $trans->add_transaction(memory::get_all());
	memory::clear();
	redirect('xferadd.php');
}

$fields = array(
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);
$form->set($fields);

$data = $_POST;
$names = $trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];

view('Confirm Inter-Account Transfer', ['data' => $data], 'txnsave.php', 'xfervrfy');

