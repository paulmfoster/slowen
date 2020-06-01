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

$month_options = array(
	array('lbl' => 'January', 'val' => 1),
	array('lbl' => 'February', 'val' => 2),
	array('lbl' => 'March', 'val' => 3),
	array('lbl' => 'April', 'val' => 4),
	array('lbl' => 'May', 'val' => 5),
	array('lbl' => 'June', 'val' => 6),
	array('lbl' => 'July', 'val' => 7),
	array('lbl' => 'August', 'val' => 8),
	array('lbl' => 'September', 'val' => 9),
	array('lbl' => 'October', 'val' => 10),
	array('lbl' => 'November', 'val' => 11),
	array('lbl' => 'December', 'val' => 12)
);

for ($i = 2016; $i < 2050; $i++) {
	$year_options[] = array('lbl' => $i, 'val' => $i);
}

// $state == 0
$fields = array(
	'month' => array(
		'name' => 'month',
		'type' => 'select',
		'options' => $month_options
	),
	'year' => array(
		'name' => 'year',
		'type' => 'select',
		'options' => $year_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Calculate'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

$state = 0;

if (isset($_POST['s1'])) {

	$data = $sm->audit($_POST['year'], $_POST['month']);

	$state = 1;

} // !empty($_POST)

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'month';
$page_title = 'Audit';
$view_file = 'views/audit.view.php';
$help_file = 'views/audith.view.php';
include 'view.php';

