<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
	relocate('index.php');
}

$trans = load_model('transaction');

$txns = $trans->get_transaction($txnid);
if ($txns[0]['split'] == 1) {
	$splits = $trans->get_splits($txns[0]['txnid']);
}
else {
	$splits = NULL;
}

$page_title = 'Show Transaction';
$view_file = view_file('txnshow');
include 'view.php';

