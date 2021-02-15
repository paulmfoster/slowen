<?php

include 'init.php';
$rpt = load_model('report');

$payees = $rpt->get_payees();
$categories = $rpt->get_accounts();

$payee_options = array();
foreach ($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$cat_options = array();
foreach ($categories as $cat) {
	$cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
		'val' => $cat['acct_id']);
}

$fields = array(
	'payee' => array(
		'name' => 'payee',
		'type' => 'select',
		'options' => $payee_options
	),
	'category' => array(
		'name' => 'category',
		'type' => 'select',
		'options' => $cat_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Search By Payee'
	),
	's2' => array(
		'name' => 's2',
		'type' => 'submit',
		'value' => 'Search By Category'
	)
);
$form->set($fields);

$page_title = 'Search';
$view_file = view_file('search');
include 'view.php';

