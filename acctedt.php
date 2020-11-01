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

$parents = $sm->get_parents();
$parent_options = array();
foreach ($parents as $parent) {
	$parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
}

$acct_type_options = array();
foreach ($acct_types as $key => $value) {
	$acct_type_options[] = array('lbl' => $value, 'val' => $key);
}

$fields = array(
	'acct_id' => array(
		'name' => 'acct_id',
		'type' => 'hidden'
	),
	'parent' => array(
		'name' => 'parent',
		'type' => 'select',
		'options' => $parent_options
	),
	'open_dt' => array(
		'name' => 'open_dt',
		'type' => 'date'
	),
	'recon_dt' => array(
		'name' => 'recon_dt',
		'type' => 'date'
	),
	'acct_type' => array(
		'name' => 'acct_type',
		'type' => 'select',
		'options' => $acct_type_options
	),
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'descrip' => array(
		'name' => 'descrip',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 255 
	),
	'open_bal' => array(
		'name' => 'open_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12 
	),
	'rec_bal' => array(
		'name' => 'rec_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12 
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

if (isset($_GET['acct_id'])) {
	$acct = $sm->get_account($_GET['acct_id']);
}
elseif (isset($_POST['s1'])) {
	if ($sm->update_account($_POST)) {
		emsg('S', "Account edits SAVED");
		header('Location: ' . $base_url . 'accounts.php');
		exit();
	}
	else {
		emsg('F', "Account update FAILED");
		header('Location: ' . $base_url . 'accounts.php');
		exit();
	}
}	
else {
	// shouldn't happen
	header('Location: ' . $base_url . 'accounts.php');
}



/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = '';
$page_title = 'Edit Account';
$view_file = 'views/acctedt.view.php';
$helpfile = 'views/acctedth.view.php';
include 'view.php';

