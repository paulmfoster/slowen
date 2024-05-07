<?php

// NOTE: Do the actual payee adding.

include 'init.php';

$payee = model('payee', $db);

if (isset($_POST['s1'])) {
    $payee->add_payee($_POST['name']);
}

redirect('addpayee.php');

