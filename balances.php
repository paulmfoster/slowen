<?php

include 'init.php';

$state = $_POST['s1'] ?? 'alfa';

if ($state != 'alfa') {
	if (!empty($_POST['last_dt'])) {
		$x_today = pdate::reformat('Y-m-d', $_POST['last_dt'], 'm/d/y');
		$bals = $sm->get_balances($_POST['last_dt']);
	}
	else {
		$x_today = pdate::get(pdate::now(), 'm/d/y');
		$bals = $sm->get_balances();
	}

	if ($bals === FALSE) {
		emsg('F', 'Date is too early to show balances');
		$state = 'alfa';
	}
	else {
		$nbals = count($bals);
	}
}

if ($state == 'alfa') {
	$fields = [
		'last_dt' => [
			'name' => 'last_dt',
			'type' => 'date'
		],
		's1' => [
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Compute'
		]
	];

	$form = new form($fields);
	$today = pdate::get(pdate::now(), 'Y-m-d');
	$stage = 'pick_date';
}
else {
	$stage = 'show_bals';
}



$focus_field = 'last_dt';
$help_file = 'views/balancesh.view.php';
$page_title = 'List Balances';
$view_file = 'views/balances.view.php';
include 'view.php';

