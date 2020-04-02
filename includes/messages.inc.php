<?php

/**
 * @copyright 2013, 2017 by Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE
 * @package apps
 * @version 2.0
 */

/**
 * Used in debugging to dump a variable or array
 *
 * @param string $caption Say something about the variable to detail
 * @param mixed $var Variable to examine
 */

function instrument($caption, $var)
{
	print $caption . '<br>' . PHP_EOL;
	print '<pre>' . PHP_EOL;
	print_r($var);
	print '</pre>' . PHP_EOL;
}

/**
 * emsg()
 *
 * Quick way to copy a message to the SESSION variable, for display
 * later, possibly on a page the user is diverted to.
 *
 * @param string $message The message to be appended.
 *
 */

function emsg($message)
{
	$_SESSION['messages'][] = $message;
}

/**
 * show_messages()
 *
 * Show messages in the SESSION variable.
 *
 * Messages are start with a code number which allows the programmer to
 * find precisely where a message is defined in the code. The codes
 * start with either an 'S' for success message, or an 'F' for failure
 * messages.
 *
 */

function show_messages()
{
	if (isset($_SESSION['messages'])) {
		foreach ($_SESSION['messages'] as $message) {
			if (strpos($message, 'S') === 0) {
				$class = 'success-message';
			}
			elseif (strpos($message, 'F') === 0) {
				$class = 'failure-message';
			}
			else {
				// shouldn't happen
				$class = 'none';
			}
			echo '<div class="' . $class . '">' . PHP_EOL;
			echo $message . '<br/>';
			echo '</div>' . PHP_EOL;
		} // foreach
	}
	unset($_SESSION['messages']);
}

