<?php

include 'init.php';

$sched = model('scheduled', $db);

$r = $sched->fetch_scheduled();
$page_title = 'Activate Scheduled Transactions';
$return = 'actsched2.php';

include VIEWDIR . 'schact.view.php';
