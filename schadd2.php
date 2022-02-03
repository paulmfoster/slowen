<?php

// scheduled transaction entered

include 'init.php';
$s1 = fork('s1', 'P', 'schadd.php');

$sched = model('scheduled');

$status = $sched->add_scheduled($_POST);
if ($status) {
	emsg('S', "Scheduled transaction added.");
}
redirect('schadd.php');

