<?php

include 'init.php';

$trans = model('transaction', $db);

$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
    redirect('index.php');
}

$trans->void_transaction($txnid);
redirect('showtxn.php?txnid=' . $txnid);

