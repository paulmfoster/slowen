<?php

include 'init.php';
$accts = load_model('account');

$accounts = $accts->get_accounts();

$acct_options = array();
foreach ($accounts as $account) {
	$acct_options[] = array('lbl' => $account['name'] . ' ' . $atnames[$account['acct_type']], 'val' => $account['acct_id']);
}

$fields = array(
	'acct_id' => array(
		'name' => 'acct_id',
		'type' => 'select',
		'options' => $acct_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Edit Account'
	)
);

$form = new form($fields);

$page_title = 'Edit Account: Select Account';
$view_file = view_file('accounts');
$destination = 'acctedt2.php';
include 'view.php';

