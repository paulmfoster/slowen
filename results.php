<?php

include 'init.php';
$rpt = load_model('report');

$acct = $_POST['category'] ?? NULL;
$payee = $_POST['payee'] ?? NULL;

if (!is_null($acct)) {
	$transactions = $rpt->get_transactions($acct, 'C');
}
elseif (!is_null($payee)) {
	$transactions = $rpt->get_transactions($payee, 'P');
}
else {
	relocate('index.php');
}

$page_title = 'Search Results';
$view_file = view_file('results');
include 'view.php';


