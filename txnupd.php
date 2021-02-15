<?php

include 'init.php';
$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
	relocate('index.php');
}
$trans = load_model('transaction');
$trans->update_transaction($_POST);
relocate('txnshow.php?txnid=' . $_POST['txnid']);

