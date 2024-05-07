<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
    redirect('index.php');
}

$trans = model('transaction', $db);

$txns = $trans->get_transaction($txnid);
if ($txns[0]['split'] == 1) {
    $splits = $trans->get_splits($txns[0]['id']);
}
else {
    $splits = NULL;
}

$page_title = 'Show Transaction';
include VIEWDIR . 'txnshow.view.php';

