<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
    redirect('index.php');
}

$trans = model('transaction', $db);
$txns = $trans->get_transaction($txnid);

// can't void a reconciled transaction
if ($txns[0]['status'] == 'R') {
    emsg('F', "Transaction is reconciled and can't be voided.");
    redirect('showtxn.php?txnid=' . $txnid);
}

if ($txns[0]['split'] == 1) {
    $splits = $trans->get_splits($txns[0]['id']);
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

$page_title = 'Void Transaction';
$return = 'voidtxn2.php';
include VIEWDIR . 'txnvoid.view.php';

