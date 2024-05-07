<?php

include 'init.php';

$trans = model('addtxn', $db);
$confirm = $_POST['confirm'] ?? NULL;
if (!is_null($confirm)) {
    $txnid = $trans->add_transaction(memory::get_all());
    memory::clear();
}
else {
    emsg('Transaction save aborted.');
}

redirect('addtxn.php');
