<?php

/**********************************************************************
 * Section I: Copyright Section
 **********************************************************************/

/**
 * @package apps
 * @copyright  2017, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 
 */

/**********************************************************************
 * Section II: Initialization File Section
 *
 * Include the common initialization file here
 **********************************************************************/

include 'init.php';


/**********************************************************************
 * Section III: Includes Section
 *
 * All files which should be included should be included here.
 * These are bare includes, not classes which need to be linked
 * into the superobject.
 * See lower section(s) for classes linked to the superobject.
 **********************************************************************/


/**********************************************************************
 * Section IV: Class Instantiation Section
 *
 * Instantiate whatever classes are needed for this application.
 * Link them to the superobject.
 **********************************************************************/


/**********************************************************************
 * Section V: User Input Handling Section
 *
 * Check POST or GET variables, handle as needed.
 **********************************************************************/

/**********************************************************************
 * Section VI: Views Section
 *
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'query';
$page_title = 'Raw Query, Screen 1';
$views = array();
$views[] = $loc['apps']['directory'] . 'head.view.php';
$views[] = $app_dir . 'views/slquery1.view.php';
$views[] = $loc['apps']['directory'] . 'footer.view.php';

// show each view in turn
$ct = count($views);
for ($i = 0; $i < $ct; $i++) {
	include $views[$i];
}

