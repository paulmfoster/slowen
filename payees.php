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

$payees = $sm->get_payees();

$id_options = array();
foreach ($payees as $payee) {
	$id_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$fields = array(
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'select',
		'options' => $id_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Add Payee'
	),
	's2' => array(
		'name' => 's2',
		'type' => 'submit',
		'value' => 'Edit Payee'
	),
	's3' => array(
		'name' => 's3',
		'type' => 'submit',
		'value' => 'Delete Payee'
	)
);

$form = new form($fields);

/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (!empty($_POST['s1'])) {
	header('Location: payadd.php');
	exit();
}
elseif (!empty($_POST['s2'])) {
	header('Location: payedt.php?payee_id=' . $_POST['payee_id']);
	exit();
}
elseif (!empty($_POST['s3'])) {
	header('Location: paydel.php?payee_id=' . $_POST['payee_id']);
	exit();
}

/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'name';
$page_title = 'Payees';
$help_file = 'views/payeesh.view.php';
$view_file = 'views/payees.view.php';
include 'view.php';

