<?php

include 'init.php';

$id = $_GET['id'] ?? NULL;
if (is_null($id)) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }
    else {
        redirect('regsel.php');
    }
}

$txns = model('transaction', $db);

$acct = $txns->get_account($id);
$r = $txns->get_transactions($id, 'F');

$page_title = 'Account Register';
include VIEWDIR . 'register.view.php';


