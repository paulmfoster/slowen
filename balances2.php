<?php

include 'init.php';
$rpt = load_model('report');

if (!empty($_POST['last_dt'])) {
	$today = $_POST['last_dt'];
	$bals = $rpt->get_balances($_POST['last_dt']);
}
else {
	$today = pdate::now2iso();
	$bals = $rpt->get_balances();
}

if ($bals === FALSE) {
	emsg('F', 'Date is too early to show balances');
	relocate('balances.php');
}
else {
	$nbals = count($bals);
}

$page_title = 'Balances';
$view_file = view_file('balances2');
include 'view.php';

