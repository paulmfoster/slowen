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
	'last_dt' => array(
		'name' => 'last_dt',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Compute'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_POST['s1'])) {
	if (!empty($_POST['last_dt'])) {
		$today = date::reformat($date_template, $_POST['last_dt'], 'Y-m-d');
		$x_today = date::reformat($date_template, $_POST['last_dt'], 'm/d/y');

		$bals = $sm->get_balances($today);
	}
	else {
		$x_today = date::get(date::now(), 'm/d/y');
		$bals = $sm->get_balances();
	}
	$nbals = count($bals);
	$stage = 'show_bals';
}
else {
	$stage = 'pick_date';
}


/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'last_dt';
$help_file = 'views/balancesh.view.php';
$page_title = 'List Balances';
$view_file = 'views/balances.view.php';
include 'view.php';

