<?php

include 'init.php';

$month = $_POST['month'] ?? NULL;
$year = $_POST['year'] ?? NULL;

if (is_null($year)) {
    redirect('index.php');
}

$audit = model('audit', $db);

if (is_null($month)) {
    $data = $audit->yearly_audit($_POST['year']);
}
else {
    $data = $audit->monthly_audit($_POST['year'], $_POST['month']);
}

$print_filename = PRINTDIR . $data['filename'];
$web_filename = PRINTDIR . $data['filename'];

$audit->print_audit($data, $print_filename);

$d = [
    'data' => $data,
    'web_filename' => $web_filename
];

$page_title = 'Audit';

include VIEWDIR . 'audshow.view.php';

