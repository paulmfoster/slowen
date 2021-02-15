<?php

include 'init.php';

$continue = $_POST ?? FALSE;
if (!$continue) {
	relocate('txnadd.php');
}

$trans = load_model('addtxn');
$txnid = $trans->add_transaction(memory::get_all());
memory::clear();

relocate('txnshow.php?txnid=' . $txnid);
