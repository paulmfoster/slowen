<?php

include 'init.php';

if (isset($_POST['s1'])) {
    $account = model('account', $db);
    $account->add_account($_POST);
}
redirect('listacct.php');

