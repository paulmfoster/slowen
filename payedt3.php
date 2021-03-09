<?php

// payee has been edited; now show the payee with edits

include 'init.php';
$pay = model('payee');

if (isset($_POST['s1'])) {
	$pay->update_payee($_POST['payee_id'], $_POST['name']);
}
else {
	redirect('index.php');
}

$payee = $pay->get_payee($_POST['payee_id']);

view('Show Payee', ['payee' => $payee], '', 'payshow');
