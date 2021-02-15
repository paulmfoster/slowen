<?php

include 'init.php';
$trans = load_model('addtxn');

$fields = array(
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);
$form->set($fields);

$data = $_POST;
memory::merge($_POST);
memory::set('amount', - $_POST['dr_amount']);
$names = $trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];

$page_title = 'Confirm Inter-Account Transfer';
$view_file = view_file('xfervrfy');
include 'view.php';

