<?php

// NOTE: Do the appropriate action with payee.

include 'init.php';

$payee = model('payee', $db);

$edit = $_POST['edit'] ?? NULL;
$delete = $_POST['delete'] ?? NULL;

if (!is_null($edit)) {
    redirect('editpayee.php?id=' . $_POST['id']);
}
elseif (!is_null($delete)) {
    redirect('delpayee.php?id=' . $_POST['id']);
}
else {
    redirect('listpayee.php');
}

