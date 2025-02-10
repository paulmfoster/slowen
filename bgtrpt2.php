<?php 

include 'init.php';

$report = model('report', $db);

fork('s1', 'P', 'index.php');

$from = $_POST['from'];
$to = $_POST['to'];
$category = $_POST['category'];

list($txns, $balance) = $report->budget($from, $to, $category);

$page_title = 'Budget Query Results';
include VIEWDIR . 'bgtrpt2.view.php';

