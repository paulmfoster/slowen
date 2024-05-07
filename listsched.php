<?php

include 'init.php';

$sched = model('scheduled', $db);
$list = $sched->fetch_scheduled();
$page_title = 'Scheduled Transactions List';

include VIEWDIR . 'schlist.view.php';

