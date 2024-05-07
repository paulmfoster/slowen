<?php

include 'init.php';

$txnid = $_POST['txnid'] ?? NULL;
if (is_null($txnid)) {
    redirect('index.php');
}
$trans = model('transaction', $db);

$trans->update_transaction($_POST);
redirect('showtxn.php?txnid=' . $txnid);

