<?php

include 'init.php';
$trans = model('addtxn');
memory::merge($_POST);

if ($cfg['confirm_transactions'] == 0) {
	$txnid = $trans->add_transaction(memory::get_all());
	memory::clear();
	redirect('othadd.php');
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
$names = $trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];
$data['status_descrip'] = $statuses[$data['status']];

if (isset($data['split']) && $data['max_splits'] > 0) {
	for ($e = 0; $e < $data['max_splits']; $e++) {
		$names = $trans->get_split_names($data['split_payee_id'][$e], $data['split_to_acct'][$e]);
		$data['split_to_name'][$e] = $names['split_to_name'];
		$data['split_payee_name'][$e] = $names['split_payee_name'];
	}
}

view('Confirm Transaction', ['data' => $data], 'txnsave.php', 'othvrfy');

