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

$fields = array(
	'acct_id' => array(
		'name' => 'acct_id',
		'type' => 'hidden'
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Delete'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_GET['acct_id'])) {
	$acct = $sm->get_account($_GET['acct_id']);
}
elseif (isset($_POST['s1'])) {
	if ($sm->delete_account($_POST['acct_id'])) {
		header('Location: ' . $base_url . 'accounts.php');
		exit();
	}
	else {
		emsg($messages['F1626']);
		header('Location: ' . $base_url . 'accounts.php');
		exit();
	}
}
else {
	// shouldn't happen
	header('Location: ' . $base_url . 'accounts.php');
	exit();
}


/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = '';
$page_title = 'Delete Account';
$view_file = 'views/acctdel.view.php';
include 'view.php';

