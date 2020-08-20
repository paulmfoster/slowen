<?php

include 'init.php';

if (!isset($_SESSION['form_data'])) {
	header('Location: ' . $base_url . 'txnadd.php');
	exit();
}

if (isset($_POST['s1'])) {
	$_SESSION['form_data'] = array_merge($_SESSION['form_data'], $_POST);
	$sm->add_transaction($_SESSION['form_data']);
	unset($_SESSION['form_data']);
	header('Location: ' . $base_url . 'txnadd.php');
	exit();
}
else {

	$fields = array(
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Confirm'
		)
	);

	$form = new form($fields);

	$data = $_SESSION['form_data'];
	$names = $sm->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
	$data['from_acct_name'] = $names['from_acct_name'];
	$data['to_acct_name'] = $names['to_acct_name'];
	$data['payee_name'] = $names['payee_name'];
	$data['status_descrip'] = $statuses[$_SESSION['form_data']['status']];
	$data['x_txn_dt'] = pdate::reformat('Y-m-d', $_SESSION['form_data']['txn_dt'], 'm/d/y');
	$data['x_recon_dt'] = !empty($_SESSION['form_data']['recon_dt']) ? pdate::reformat('Y-m-d', $_POST['recon_dt'], 'm/d/y') : '';

	if (isset($data['split']) && $data['max_splits'] > 0) {
		for ($e = 0; $e < $data['max_splits']; $e++) {
			$data['split_to_name'][$e] = $sm->get_acct_name($data['split_to_acct'][$e]);
			$data['split_payee_name'][$e] = $sm->get_payee_name($data['split_payee_id'][$e]);
		}
	}
}

$page_title = 'Confirm Transaction';
$view_file = 'views/txnvrfy.view.php';
include 'view.php';

