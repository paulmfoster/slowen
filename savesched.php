<?php

include 'init.php';
$sched = model('scheduled', $db);

$s1 = fork('s1', 'P', 'addsched.php');
$status = $sched->add_scheduled($_POST);
if ($status) {
    emsg('S', "Scheduled transaction added.");
}
redirect('addsched.php');
