<?php

include 'init.php';

$id = $_POST['id'] ?? NULL;
if (is_null($id))
    redirect('editbline.php');

$bg = model('budget', $db);
$result = $bg->update_account($_POST);
if ($result)
    emsg('S', 'Budget account edits saved.');
else
    emsg('F', 'Update of budget account failed.');

redirect('editbline.php');

