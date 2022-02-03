<?php

include 'init.php';
$sch = model('scheduled');

$r = $sch->fetch_scheduled();

view('Activate Scheduled Transactions', ['r' => $r], 'schact2.php', 'schact');
