<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
	relocate('index.php');
}

$trans = load_model('transaction');
$txns = $trans->get_transaction($txnid);
if ($txns[0]['split'] == 1) {
	$splits = $sm->get_splits($txns[0]['txnid']);
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

$page_title = 'Void Transaction';
$view_file = view_file('txnvoid');
include 'view.php';

