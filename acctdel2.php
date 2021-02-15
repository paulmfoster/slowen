<?php

include 'init.php';
$accts = load_model('account');

$acct_id = $_POST['acct_id'] ?? NULL;
if (!is_null($acct_id)) {
	$acct = $accts->get_account($acct_id);
	if ($acct === FALSE) {
		relocate('acctdel.php');
	}
}
else {
	relocate('acctdel.php');
}

$acct['x_acct_type'] = $acct_types[$acct['acct_type']];

$fields = array(
	'acct_id' => array(
		'name' => 'acct_id',
		'type' => 'hidden',
		'value' => $acct_id
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Delete'
	)
);

$form->set($fields);

$page_title = 'Delete Account';
$view_file = view_file('acctdel2');
include 'view.php';
