<?php

include 'init.php';

$id = $_POST['id'] ?? NULL;
if (is_null($id))
    redirect('editsched.php');

$scheduled = model('scheduled', $db);

$result = $scheduled->update_scheduled($_POST);

if ($result)
    emsg('S', 'Scheduled transaction updated.');
else
    emsg('F', 'Scheduled transaction update FAILED.');

redirect('editsched.php');

