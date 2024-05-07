<?php

include 'init.php';

$sched = model('scheduled', $db);

$r = $sched->fetch_scheduled();
$page_title = 'Delete Scheduled Transactions';
$return = 'delsched2.php';

include VIEWDIR . 'schdel.view.php';
