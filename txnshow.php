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


/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_GET)) {
	if (isset($_GET['txnid'])) {
		$acct_id = $_GET['acct_id'];
		$txns = $sm->get_transaction($_GET['txnid']);
		if ($txns[0]['split'] == 1) {
			$splits = $sm->get_splits($txns[0]['txnid']);
		}
		else {
			$splits = NULL;
		}
	}
	else {
		header('Location: ' . $base_url . 'acctlist.php');
		exit();
	}
}
else {
	header('Location: ' . $base_url . 'acctlist.php');
	exit();
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Show Transaction';
$view_file = 'views/txnshow.view.php';
include 'view.php';

