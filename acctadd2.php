<?php

include 'init.php';
$accts = load_model('account');

if (isset($_POST['s1'])) {
	$accts->add_account($_POST);
}

relocate('index.php');

