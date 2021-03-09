<?php

include 'init.php';
$pay = model('payee');

$payee_id = $_POST['payee_id'] ?? NULL;
if (!is_null($payee_id)) {
	$pay->delete_payee($_POST['payee_id']);
}

redirect('paydel.php');

