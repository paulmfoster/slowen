<?php

include 'init.php';
$audit = load_model('audit');

$month = $_POST['month'] ?? NULL;
$year = $_POST['year'] ?? NULL;

if (is_null($year)) {
	relocate('index.php');
}

if (is_null($month)) {
	$data = $audit->yearly_audit($_POST['year']);
}
else {
	$data = $audit->monthly_audit($_POST['year'], $_POST['month']);
}

$print_filename = $cfg['base_dir'] . $cfg['printdir'] . $data['filename'];
$web_filename = $cfg['base_url'] . $cfg['printdir'] . $data['filename'];
// $pdf->output($filename);

$audit->print_audit($data, $print_filename);

$page_title = 'Audit';
$view_file = view_file('audit2');
include 'view.php';

