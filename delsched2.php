<?php

include 'init.php';

$sched = model('scheduled', $db);

fork('s1', 'P', 'delsched.php');

$status = $sched->delete_scheduled($_POST);
if ($status) {
    emsg('S', 'Scheduled transactions deleted.');
}

redirect('listsched.php');


