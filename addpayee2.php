<?php

// NOTE: Do the actual payee adding.

include 'init.php';

$payee = model('payee', $db);

if (isset($_POST['s1'])) {
    $result = $payee->add_payee($_POST['name']);
}

if ($result == FALSE)
    emsg('F', "There is already a payee by that name.");

redirect('addpayee.php');

