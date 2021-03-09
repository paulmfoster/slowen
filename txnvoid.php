<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
	redirect('index.php');
}

$trans = model('transaction');
$txns = $trans->get_transaction($txnid);
if ($txns[0]['split'] == 1) {
	$splits = $trans->get_splits($txns[0]['txnid']);
}
else {
	$splits = [];
}

$fields = array(
	'txnid' => array(
		'name' => 'txnid',
		'type' => 'hidden',
		'value' => $txnid
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);
$form->set($fields);

view('Void Transaction', ['txns' => $txns, 'splits' => $splits], 'txnvoid2.php', 'txnvoid');

