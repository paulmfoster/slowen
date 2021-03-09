<?php

include 'init.php';
$pay = model('payee');

if (isset($_POST['s1'])) {
	$pay->add_payee($_POST['name']);
}

redirect('payadd.php');

