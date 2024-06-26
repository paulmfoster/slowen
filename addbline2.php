<?php

include 'init.php';

$fields = ['acctname', 'period'];
if (!filled_out($_POST, $fields)) {
    emsg('F', 'Form is missing required fields. Aborted.');
}
else {
    $bg = model('budget', $db);
    $result = $bg->add_account($_POST);
    if ($result == TRUE)
        emsg('S', 'Account added.');
    
    else
        emsg('F', 'Account addition failed.');
}

redirect('addbline.php');

