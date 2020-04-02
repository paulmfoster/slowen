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

if (count($_POST) == 0) {

	$payees = $sm->get_payees();
	$categories = $sm->get_accounts();

	$vendor_options = array();
	foreach ($payees as $payee) {
		$vendor_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
	}

	$cat_options = array();
	foreach ($categories as $cat) {
		$cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
			'val' => $cat['acct_id']);
	}

	$fields = array(
		'vendor' => array(
			'name' => 'vendor',
			'type' => 'select',
			'options' => $vendor_options
		),
		'category' => array(
			'name' => 'category',
			'type' => 'select',
			'options' => $cat_options
		),
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Search By Vendor'
		),
		's2' => array(
			'name' => 's2',
			'type' => 'submit',
			'value' => 'Search By Category'
		)
	);

	$form = new form($fields);

} 
else {
	if (isset($_POST['s1'])) {
		$param = '?vendor=' . $_POST['vendor'];
	}
	elseif (isset($_POST['s2'])) {
		$param = '?category=' . $_POST['category'];
	}

	header('Location: ' . 'results.php' . $param);
	exit();
}


/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Search';
$view_file = 'views/search.view.php';
$helpfile = 'views/searchh.view.php';
include 'view.php';


