<?php

/**********************************************************************
 * Copyright Section
 **********************************************************************/

/**
 * @package apps
 * @copyright  2019, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 2.0
 */

/**********************************************************************
 * Initialization File Section
 *
 * Include the common initialization file here
 **********************************************************************/

include 'init.php';

/**********************************************************************
 * Access Section
 *
 * Is it okay to access this page?
 **********************************************************************/

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

$fields = array(
	'txnid' => array(
		'name' => 'txnid',
		'type' => 'hidden'
	),
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'hidden'
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Confirm'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (!empty($_GET)) {
	if (isset($_GET['txnid'])) {
		$txns = $sm->get_transaction($_GET['txnid']);
		if ($txns[0]['split'] == 1) {
			$splits = $sm->get_splits($txns[0]['txnid']);
		}
	}
	else {
		$_SESSION['messages'][] = 'FGET was ' . var_export($_GET, TRUE);
		$_SESSION['messages'][] = 'FPOST was ' . var_export($_POST, TRUE);
		header('Location: ' . $base_url . 'index.php');
		exit();
	}
}
elseif (!empty($_POST) && $_POST['s1'] == 'Confirm') {
	$sm->void_transaction($_POST['txnid']);
	header('Location: ' . $base_url . 'register.php?acct_id=' . $_POST['from_acct']);
	exit();
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Void Transaction';
$view_file = 'views/txnvoid.view.php';
include 'view.php';

