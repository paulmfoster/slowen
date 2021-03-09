<?php

// user has specified yearly or monthly criteria
// now we prepare the data and show it

include 'init.php';
$audit = model('audit');

$month = $_POST['month'] ?? NULL;
$year = $_POST['year'] ?? NULL;

if (is_null($year)) {
	redirect('index.php');
}

if (is_null($month)) {
	$data = $audit->yearly_audit($_POST['year']);
}
else {
	$data = $audit->monthly_audit($_POST['year'], $_POST['month']);
}

$print_filename = $cfg['base_dir'] . $cfg['printdir'] . $data['filename'];
$web_filename = $cfg['base_url'] . $cfg['printdir'] . $data['filename'];

$audit->print_audit($data, $print_filename);

$d = [
	'data' => $data,
	'web_filename' => $web_filename
];

view('Audit', $d, 'audit2.php', 'audit2');

