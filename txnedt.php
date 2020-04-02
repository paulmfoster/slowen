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


/**********************************************************************
 * User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

if (isset($_GET['txnid'])) {

	$acct_id = $_GET['acct_id'];
	$txns = $sm->get_transaction($_GET['txnid']);
	$max_txns = count($txns);

	$payees = $sm->get_payees();
	$payee_options = array();
	foreach($payees as $payee) {
		$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
	}

	if ($max_txns > 1) {

		// inter-account transfer

		$iaxfields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'hidden'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'hidden'
			),
			'iaxid' => array(
				'name' => 'iaxid[]',
				'type' => 'hidden'
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden'
			),
			'txnid' => array(
				'name' => 'txnid',
				'type' => 'hidden'
			),
			'txn_dt' => array(
				'name' => 'txn_dt[]',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10
			),
			'checkno' => array(
				'name' => 'checkno[]',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id' => array(
				'name' => 'payee_id[]',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo[]',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save Edits'
			)
		);

		$iaxform = new form($iaxfields);
		$view_file = 'views/iaxedt.view.php';

	}
	else {

		// single transaction

		$atnames = array(
			' ' => '(none)',
			'I' => '(inc)',
			'E' => '(exp)',
			'L' => '(liab)',
			'A' => '(asset)',
			'Q' => '(eqty)',
			'R' => '(ccard)',
			'C' => '(chkg)',
			'S' => '(svgs)'
		);

		$status_options = array();
		foreach ($statuses as $key => $value) {
			$status_options[] = array('lbl' => $value, 'val' => $key);
		}

		$to_accts = $sm->get_to_accounts();
		$to_options = array();
		foreach ($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'hidden'
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden'
			),
			'txnid' => array(
				'name' => 'txnid',
				'type' => 'hidden'
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10
			),
			'checkno' => array(
				'name' => 'checkno',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'status' => array(
				'name' => 'status',
				'type' => 'select',
				'options' => $status_options
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'dr_amount' => array(
				'name' => 'dr_amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'cr_amount' => array(
				'name' => 'cr_amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'amount' => array(
				'name' => 'amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save Edits'
			)
		);

		$form = new form($fields);
		$view_file = 'views/txnedt.view.php';


		if ($txns[0]['split']) {

			// splits

			$splits = $sm->get_splits($txns[0]['txnid']);
			$max_splits = count($splits);
		
			$split_to_options = array();
			foreach ($to_accts as $to_acct) {
				$split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
					'val' => $to_acct['acct_id']);
			}

			$sfields = array(
				'split_id' => array(
					'name' => 'split_id[]',
					'type' => 'hidden'
				),
				'split_payee_id' => array(
					'name' => 'split_payee_id[]',
					'type' => 'select',
					'options' => $payee_options
				),
				'split_to_acct' => array(
					'name' => 'split_to_acct[]',
					'type' => 'select',
					'options' => $split_to_options
				),
				'split_memo' => array(
					'name' => 'split_memo[]',
					'type' => 'text',
					'size' => 35,
					'maxlength' => 35
				),
				'split_amount' => array(
					'name' => 'split_amount[]',
					'type' => 'text',
					'size' => 12,
					'maxlength' => 12
				)
			);

			$sform = new form($sfields);
		}
	}
}
elseif (isset($_POST['s1'])) {

	$sm->update_transaction($_POST);
	header('Location: ' . $base_url . "txnshow.php?acct_id={$_POST['acct_id']}&txnid={$_POST['txnid']}");
	exit();

}


/**********************************************************************
 * Screen Setup Section
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = '';
$page_title = 'Transaction Edit';
include 'view.php';

