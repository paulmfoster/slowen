<?php

include 'init.php';
$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
	redirect('index.php');
}
$trans = model('transaction');
$trans->update_transaction($_POST);
redirect('txnshow.php?txnid=' . $_POST['txnid']);

