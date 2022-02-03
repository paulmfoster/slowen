<?php

include 'init.php';
$sch = model('scheduled');

$r = $sch->fetch_scheduled();

view('Delete Scheduled Transactions', ['r' => $r], 'schdel2.php', 'schdel');
