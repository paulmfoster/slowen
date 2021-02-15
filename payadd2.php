<?php

include 'init.php';
$pay = load_model('payee');

if (isset($_POST['s1'])) {
	$pay->add_payee($_POST['name']);
}

relocate('payadd.php');

