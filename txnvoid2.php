<?php

include 'init.php';

$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
	redirect('index.php');
}

$trans = model('transaction');
$trans->void_transaction($_POST['txnid']);
redirect("txnshow.php?txnid={$_POST['txnid']}");

