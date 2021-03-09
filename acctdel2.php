<?php

include 'init.php';
$accts = model('account');

$acct_id = $_POST['acct_id'] ?? NULL;
if (!is_null($acct_id)) {
	$acct = $accts->get_account($acct_id);
	if ($acct === FALSE) {
		redirect('acctdel.php');
	}
}
else {
	redirect('acctdel.php');
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

view('Delete Account', ['acct' => $acct], 'acctdel3.php', 'acctdel2');

