<?php

// show search results

include 'init.php';
$rpt = model('report');

$acct = $_POST['category'] ?? NULL;
$payee = $_POST['payee'] ?? NULL;

if (!is_null($acct)) {
	$transactions = $rpt->get_transactions($acct, 'C');
}
elseif (!is_null($payee)) {
	$transactions = $rpt->get_transactions($payee, 'P');
}
else {
	redirect('index.php');
}

view('Search Results', ['transactions' => $transactions], '', 'results');



