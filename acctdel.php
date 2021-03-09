<?php

include 'init.php';
$accts = model('account');

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
		'value' => 'Select'
	)
);

$form->set($fields);

view('Delete Account: Select Account', [], 'acctdel2.php', 'accounts');

