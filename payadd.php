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
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Save'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_POST['s1'])) {
	$sm->add_payee($_POST['name']);
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'name';
$page_title = 'Add Payee';
$help_file = 'views/payaddh.view.php';
$view_file = 'views/payadd.view.php';
include 'view.php';

