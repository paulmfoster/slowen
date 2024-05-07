<?php

include 'init.php';

$account = model('account', $db);

if (isset($_POST['s1'])) {
    $account->delete_account($_POST['id']); 
}
redirect('listacct.php');

