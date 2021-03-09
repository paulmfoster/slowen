<?php

include 'init.php';
$rpt = model('report');

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
	redirect('balances.php');
}
else {
	$nbals = count($bals);
}

$d = [
	'today' => $today,
	'nbals' => $nbals,
	'bals' => $bals
];

view('Balances', $d, '', 'balances2');

