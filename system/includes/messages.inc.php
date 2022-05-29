<?php

/**
 * @copyright 2013, 2017 by Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE
 * @package apps
 * @version 2.0
 */


/**
 * emsg()
 *
 * Changed April 2020 to accommodate a different method of handling
 * messages.
 *
 * @param char $success_failure S for success message F for failure
 * @param int $index into an optional table for translations
 * @param string $message the error message
 *
 */

function emsg($success_failure, $message, $index = 0)
{
	$_SESSION['messages'][] = array($success_failure, $message, $index);
}

/**
 * show_messages()
 *
 * Changed April 2020 to accommodate a different message structure
 * If translations are desired, $message[2] will be the index into a
 * globally known translation table.
 *
 * Show messages in the SESSION variable.
 *
 */

function show_messages()
{
	if (isset($_SESSION['messages'])) {
		foreach ($_SESSION['messages'] as $message) {

			$text = $message[1];
			if ($message[0] == 'F') {
				$class = 'failure-message';
			}
			elseif ($message[0] == 'S') {
				$class = 'success-message';
			}
			else {
				$class = 'success-message';
			}

			echo '<div class="' . $class . '">' . PHP_EOL;
			echo $message[1] . '<br/>';
			echo '</div>' . PHP_EOL;

		} // foreach

		unset($_SESSION['messages']);
	}
}

