<?php

include 'init.php';
$accts = model('account');

if (isset($_POST['s1'])) {
	$accts->add_account($_POST);
}

redirect('index.php');

