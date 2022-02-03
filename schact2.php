<?php

include 'init.php';

fork('s1', 'P', 'schact.php');

$sch = model('scheduled');
$status = $sch->activate_scheduled($_POST);
if ($status) {
	emsg('S', 'Scheduled transactions activated.');
}

redirect('schact.php');

