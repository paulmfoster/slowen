<?php

include 'init.php';
fork('s1', 'P', 'index.php');

$rpt = model('report');
$t = $rpt->budget($_POST['from'], $_POST['to'], $_POST['category']);

view('Budget Query Results', ['txns' => $t[0], 'bal' => $t[1]], '', 'budget2');

