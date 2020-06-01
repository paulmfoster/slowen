<?php

/**********************************************************************
 * Copyright Section
 **********************************************************************/

/**
 * @package apps
 * @copyright  2017, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 2.0
 */

/**********************************************************************
 * Initialization File Section
 *
 * Include the common initialization file here
 * If we're in the common area already, comment line 2 out.
 * If we're in an application area, comment out line 1.
 **********************************************************************/

include 'init.php';

/**********************************************************************
 * Includes Section
 *
 * All files which should be included should be included here,
 * unless they have been included elsewhere before.
 **********************************************************************/

/**********************************************************************
 * Controller Variable Declaration Section
 *
 * Declare any variables here.
 **********************************************************************/

/**********************************************************************
 * Class Instantiation Section
 *
 * Instantiate whatever classes are needed for this application.
 * Link them to the superobject.
 **********************************************************************/


/**********************************************************************
 * Data Preparation Section
 *
 * Data needed for the views may be defined here.
 **********************************************************************/


/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (!empty($_POST)) {

	if (isset($_POST['split']) && $_POST['split'] == 1) {
		$_SESSION['form_data'] = $_POST;
		header('Location: ' . $base_url . 'txnsplt.php');
		exit();
	}

	$_SESSION['form_data'] = $_POST;
	header('Location: ' . $base_url . 'txnvrfy.php');
	exit();
	
}

// blank existing data
unset($_POST);
unset($_SESSION['form_data']);

$accounts = $sm->get_accounts();
$payees = $sm->get_payees();
$from_accts = $sm->get_from_accounts();
$to_accts = $sm->get_to_accounts();

$atnames = array(
	' ' => '',
	'I' => '(inc)',
	'E' => '(exp)',
	'L' => '(liab)',
	'A' => '(asset)',
	'Q' => '(eqty)',
	'R' => '(ccard)',
	'C' => '(chkg)',
	'S' => '(svgs)'
);

$from_options = array();
foreach($from_accts as $from_acct) {
	$from_options[] = array('lbl' => 
		$from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
		'val' => $from_acct['acct_id']);
}

$payee_options = array();
$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
foreach($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$to_options = array();
foreach($to_accts as $to_acct) {
	$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
		'val' => $to_acct['acct_id']);
}

$status_options = array();
foreach($statuses as $key => $value) {
	$status_options[] = array('lbl' => $value, 'val' => $key);
}

$fields = array(
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'select',
		'options' => $from_options
	),
	'txn_dt' => array(
		'name' => 'txn_dt',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
	),
	'xfer' => array(
		'name' => 'xfer',
		'type' => 'checkbox',
		'value' => 1
	),
	'split' => array(
		'name' => 'split',
		'type' => 'checkbox',
		'value' => 1
	),
	'checkno' => array(
		'name' => 'checkno',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'select',
		'options' => $payee_options
	),
	'memo' => array(
		'name' => 'memo',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'to_acct' => array(
		'name' => 'to_acct',
		'type' => 'select',
		'options' => $to_options
	),
	'line_no' => array(
		'name' => 'line_no',
		'type' => 'hidden'
	),
	'status' => array(
		'name' => 'status',
		'type' => 'select',
		'options' => $status_options
	),
	'recon_dt' => array(
		'name' => 'recon_dt',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
	),
	'dr_amount' => array(
		'name' => 'dr_amount',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'cr_amount' => array(
		'name' => 'cr_amount',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'max_splits' => array(
		'name' => 'max_splits',
		'type' => 'text',
		'size' => 2,
		'maxlength' => 2
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Save'
	)
);

$form = new form($fields);

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Enter Transaction';
$helpfile = 'views/txnaddh.view.php';
$view_file = 'views/txnadd.view.php';

include 'view.php';

