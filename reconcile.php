<?php

include 'init.php';
$rcn = load_model('recon');

// the first entry screen

$accts = $rcn->get_recon_accts();

$from_options = array();
foreach ($accts as $acct) {
	$from_options[] = array('lbl' => $acct['acct_type'] . '/' . $acct['name'],
		'val' => $acct['acct_id']);
}

$fields = array(
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'select',
		'options' => $from_options
	),
	'stmt_start_bal' => array(
		'name' => 'stmt_start_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'stmt_end_bal' => array(
		'name' => 'stmt_end_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'stmt_close_date' => array(
		'name' => 'stmt_close_date',
		'type' => 'date'
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Continue'
	)
);

$form = new form($fields);

$page_title = 'Reconcile: Enter Preliminary Data';
$view_file = view_file('prerecon');
include 'view.php';

