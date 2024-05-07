<?php

include 'init.php';

$report = model('report', $db);

fork('s1', 'P', 'index.php');
list($txns, $bal) = $report->budget($_POST['from'], $_POST['to'], $_POST['category']);
$page_title = 'Budget Query Results';
include VIEWDIR . 'bgtshow.view.php';

