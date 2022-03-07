<?php

include 'init.php';
$trans = model('addtxn');
memory::merge($_POST);
if (strlen(trim($_POST['dr_amount'])) == 0) {
	$_POST['dr_amount'] = 0;
}
memory::set('amount', - $_POST['dr_amount']);

if ($cfg['confirm_transactions'] == 0) {
	$txnid = $trans->add_transaction(memory::get_all());
	memory::clear();
	redirect('chkadd.php');
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

$names = $trans->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];

view('Confirm Check', ['data' => $data], 'txnsave.php', 'chkvrfy');


