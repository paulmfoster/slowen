<?php

// user has entered a transaction; here we save it and then show the
// user the results

include 'init.php';

$continue = $_POST ?? FALSE;
if (!$continue) {
	redirect('txnadd.php');
}

$trans = model('addtxn');
$txnid = $trans->add_transaction(memory::get_all());
memory::clear();

redirect('txnshow.php?txnid=' . $txnid);
