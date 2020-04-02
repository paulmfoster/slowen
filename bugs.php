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


if (!empty($_POST) && $_POST['s1'] == 'Submit') {

	if (empty($_POST['email'])) {
		$_SESSION['messages'][] = 'F2002 You must include your name and email.';
	}
	elseif (empty($_POST['remark'])) {
		$_SESSION['messages'][] = 'F2003 No comment = no response. Aborted.';
	}
	else {

		$msg = 'Application = ' . $_POST['app_title'] . "\n";
		$msg .= 'Name = ' . $_POST['name'] . "\n";
		$msg .= 'Email = ' . $_POST['email'] . "\n\n";
		$msg .= 'Remarks = ' . $_POST['remark'] . "\n\n";

		mail('paulf@localhost', 'Bug Report or Feature Request for ' . $_POST['app_title'], $msg);
		$_SESSION['messages'][] = 'S2001 Thanks for your feedback! It is appreciated!';
	}
}


/**********************************************************************
 * Section VI: Views Section
 *
 *
 * Set the various view variables
 * Load views.
 **********************************************************************/

$focus_field = 'name';
$page_title = 'Bug/Feature Report';

include 'views/head.view.php';
?>
<form action="bugs.php" method="post">

<h2>Indicate what you'd like to communicate below.<br/>
Be as clear as possible; your programmer does not read minds.</h2>

<input type="hidden" name="app_title" id="app_title" value="<?php echo $app_name; ?>"/>
<label for="name">Name</label>&nbsp;<input type="text" name="name" id="name" size="50"/><br/>
<label for="email">Email</label>&nbsp;<input type="text" name="email" id="email" size="50"/><br/>
<label for="remark">Remarks</label><br/>
<textarea size="1024" rows="10" cols="50" name="remark" id="remark" wrap="soft"></textarea><br/>
<input type="submit" name="s1" id="s1" value="Submit"/>

</form>
<?php
include 'views/footer.view.php';

