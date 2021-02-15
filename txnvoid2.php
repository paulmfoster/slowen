<?php

include 'init.php';

$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
	relocate('index.php');
}

$trans = load_model('transaction');
$trans->void_transaction($_POST['txnid']);
relocate("txnshow.php?txnid={$_POST['txnid']}");

