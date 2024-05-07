<?php

include 'init.php';

$id = $_POST['id'] ?? NULL;
if (!is_null($id)) {
    $payee = model('payee', $db);
    $payee->delete_payee($id);
}

redirect('listpayee.php');

