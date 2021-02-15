<?php

include 'init.php';
$accts = load_model('account');

$acct_id = $_POST['acct_id'] ?? NULL;
if (is_null($acct_id)) {
	relocate('acctedt.php');
}

$acct = $accts->get_account($acct_id);

$parents = $accts->get_parents();
$parent_options = array();
foreach ($parents as $parent) {
	$parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
}

$acct_type_options = array();
foreach ($acct_types as $key => $value) {
	$acct_type_options[] = array('lbl' => $value, 'val' => $key);
}

$fields = array(
	'acct_id' => array(
		'name' => 'acct_id',
		'type' => 'hidden',
		'value' => $acct_id
	),
	'parent' => array(
		'name' => 'parent',
		'type' => 'select',
		'options' => $parent_options
	),
	'open_dt' => array(
		'name' => 'open_dt',
		'type' => 'date'
	),
	'recon_dt' => array(
		'name' => 'recon_dt',
		'type' => 'date'
	),
	'acct_type' => array(
		'name' => 'acct_type',
		'type' => 'select',
		'options' => $acct_type_options
	),
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'descrip' => array(
		'name' => 'descrip',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 255 
	),
	'open_bal' => array(
		'name' => 'open_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12 
	),
	'rec_bal' => array(
		'name' => 'rec_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12 
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Save'
	)
);

$form->set($fields);

$page_title = 'Edit Account';
$view_file = view_file('acctedt2');
include 'view.php';

