<?php

include 'init.php';

$id = $_POST['id'] ?? NULL;
if (is_null($id))
    redirect('delbline.php');

$bg = model('budget', $db);
$result = $bg->delete_account($_POST);
if ($result)
    emsg('S', 'Budget account has been deleted.');
else
    emsg('F', 'Deletion of budget account failed.');

redirect('delbline.php');

