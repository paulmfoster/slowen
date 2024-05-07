<?php

include 'init.php';

$account = model('account', $db);

if (isset($_POST['s1'])) {
    if ($account->update_account($_POST)) {
        emsg('S', "Account edits SAVED");
    }
}	

redirect('showacct.php?id=' . $_POST['id']);

