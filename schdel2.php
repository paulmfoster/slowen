<?php

include 'init.php';

fork('s1', 'P', 'schdel.php');

$sch = model('scheduled');
$status = $sch->delete_scheduled($_POST);
if ($status) {
	emsg('S', 'Scheduled transactions deleted.');
}

redirect('schdel.php');

