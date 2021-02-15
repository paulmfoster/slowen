<?php

include 'init.php';
$pay = load_model('payee');

if (isset($_POST['s1'])) {
	$pay->update_payee($_POST['payee_id'], $_POST['name']);
}
else {
	relocate('index.php');
}

$payee = $pay->get_payee($_POST['payee_id']);

$page_title = 'Show Payee';
$view_file = view_file('payshow');
include 'view.php';
