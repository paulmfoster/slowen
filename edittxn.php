<?php

include 'init.php';

$trans = model('transaction', $db);

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
    redirect('index.php');
}

global $atnames, $statuses;

$txns = $trans->get_transaction($txnid);

if (count($txns) > 1) {
    redirect('editxfer.php?txnid=' . $txnid);
}
elseif ($txns[0]['split']) {
    redirect('editsplits.php?txnid=' . $txnid);
}
else {
    redirect('editsingle.php?txnid=' . $txnid);
}


