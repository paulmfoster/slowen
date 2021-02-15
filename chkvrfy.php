<?php

include 'init.php';
$trans = load_model('addtxn');
memory::merge($_POST);
memory::set('amount', - $_POST['dr_amount']);

$fields = array(
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);

$form->set($fields);

$data = $_POST;

$names = $trans->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];

$page_title = 'Confirm Check';
$view_file = view_file('chkvrfy');
include 'view.php';

