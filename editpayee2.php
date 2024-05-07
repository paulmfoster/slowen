<?php

include 'init.php';

if (!isset($_POST['s1']))
    redirect('listpayee.php');

$payee = model('payee', $db);
$payee->update_payee($_POST['id'], $_POST['name']); 

redirect('showpayee.php?id=' . $_POST['id']);

