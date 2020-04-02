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

$accounts = $sm->get_accounts();

$acct_options = array();
foreach ($accounts as $account) {
	$acct_options[] = array('lbl' => $account['name'], 'val' => $account['acct_id']);
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
		'value' => 'Add Account'
	),
	's2' => array(
		'name' => 's2',
		'type' => 'submit',
		'value' => 'Edit Account'
	),
	's3' => array(
		'name' => 's3',
		'type' => 'submit',
		'value' => 'Delete Account'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (!empty($_POST['s1'])) {
	header('Location: acctadd.php');
	exit();
}
elseif (!empty($_POST['s2'])) {
	header('Location: acctedt.php?acct_id=' . $_POST['acct_id']);
	exit();
}
elseif (!empty($_POST['s3'])) {
	header('Location: acctdel.php?acct_id=' . $_POST['acct_id']);
	exit();
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = '';
$page_title = 'Accounts';
$view_file = 'views/accounts.view.php';
$helpfile = 'views/accountsh.view.php';
include 'view.php';

