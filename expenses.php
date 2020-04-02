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

$stage = 1;

$temp_date = date::now();
$oto_date = date::endwk($temp_date);
$ato_date = date::get($oto_date, 'm/d/y');
$ito_date = date::get($oto_date, 'Y-m-d');
$ofrom_date = date::adddays($oto_date, -6);
$afrom_date = date::get($ofrom_date, 'm/d/y');
$ifrom_date = date::get($ofrom_date, 'Y-m-d');

$fields = array(
	'from_date' => array(
		'name' => 'from_date',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
	),
	'to_date' => array(
		'name' => 'to_date',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
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

if (!empty($_POST)) {
	$stage = 2;
	$from = date::reformat($date_template, $_POST['from_date'], 'Y-m-d');
	$to = date::reformat($date_template, $_POST['to_date'], 'Y-m-d');
	$expenses = $sm->get_expenses($from, $to);
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$view_file = 'views/expenses.view.php';
$page_title = 'Weekly Expenses';
include 'view.php';

