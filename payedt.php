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
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'hidden'
	),
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Update'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_GET['payee_id'])) {
	$payee = $sm->get_payee($_GET['payee_id']);
}
elseif (isset($_POST['s1'])) {
	$sm->update_payee($_POST['payee_id'], $_POST['name']);
	header('Location: ' . $base_url . 'payees.php');
	exit();
}
else {
	// shouldn't happen
	header('Location: ' . $base_url . 'payees.php');
	exit();
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'name';
$page_title = 'Edit Payee';
$help_file = 'views/payedth.view.php';
$view_file = 'views/payedt.view.php';
include 'view.php';

