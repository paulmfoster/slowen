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

if (!isset($_SESSION['form_data'])) {
	header('Location: ' . $base_url . 'txnadd.php');
	exit();
}

if (isset($_POST['s1'])) {
	$_SESSION['form_data'] = array_merge($_SESSION['form_data'], $_POST);
	$sm->add_transaction($_SESSION['form_data']);
	unset($_SESSION['form_data']);
	header('Location: ' . $base_url . 'txnadd.php');
	exit();
}
else {

	$fields = array(
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Confirm'
		)
	);

	$form = new form($fields);

	$data = $_SESSION['form_data'];
	$names = $sm->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
	$data['from_acct_name'] = $names['from_acct_name'];
	$data['to_acct_name'] = $names['to_acct_name'];
	$data['payee_name'] = $names['payee_name'];
	$data['status_descrip'] = $statuses[$_SESSION['form_data']['status']];
	$data['x_txn_dt'] = date::reformat($date_template, $_SESSION['form_data']['txn_dt'], 'm/d/y');
	$data['x_recon_dt'] = !empty($_SESSION['form_data']['recon_dt']) ? date::reformat($data_template, $_POST['recon_dt'], 'm/d/y') : '';

	if (isset($data['split']) && $data['max_splits'] > 0) {
		for ($e = 0; $e < $data['max_splits']; $e++) {
			$data['split_to_name'][$e] = $sm->get_acct_name($data['split_to_acct'][$e]);
			$data['split_payee_name'][$e] = $sm->get_payee_name($data['split_payee_id'][$e]);
		}
	}
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$page_title = 'Confirm Transaction';
$view_file = 'views/txnvrfy.view.php';
include 'view.php';

