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

include 'classes/date.lib.php';

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

if (isset($_POST['s1'])) {
	// user filled in splits data
	$_SESSION['form_data'] = array_merge($_SESSION['form_data'], $_POST);
	header('Location: ' . $base_url . 'txnvrfy.php');
	exit();
}

if (!isset($_SESSION['form_data'])) {
	header('Location: ' . $base_url . 'txnadd.php');
	exit();
}

$atnames = array(
	' ' => '(none)',
	'I' => '(inc)',
	'E' => '(exp)',
	'L' => '(liab)',
	'A' => '(asset)',
	'Q' => '(eqty)',
	'R' => '(ccard)',
	'C' => '(chkg)',
	'S' => '(svgs)'
);

$payees = $sm->get_payees();
$to_accts = $sm->get_split_to_accounts();
$payee_options = array();
foreach ($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$to_options = array();
foreach ($to_accts as $to_acct) {
	$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']],
		'val' => $to_acct['acct_id']);
}

$fields = array(
	'max_splits' => array(
		'name' => 'max_splits',
		'type' => 'hidden'
	),
	'split_payee_id' => array(
		'name' => 'split_payee_id[]',
		'type' => 'select',
		'options' => $payee_options
	),
	'split_to_acct' => array(
		'name' => 'split_to_acct[]',
		'type' => 'select',
		'options' => $to_options
	),
	'split_memo' => array(
		'name' => 'split_memo[]',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'split_cr_amount' => array(
		'name' => 'split_cr_amount[]',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'split_dr_amount' => array(
		'name' => 'split_dr_amount[]',
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

$form = new form($fields);


/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Splits Entry';
$view_file = 'views/txnsplt.view.php';
include 'view.php';

