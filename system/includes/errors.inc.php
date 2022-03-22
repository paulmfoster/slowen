<?php

/**
 * @copyright 2013, 2017 by Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE
 * @package apps
 */

/**
 * Send a trace to the log file.
 */

function trace()
{
	$msg = date('c');
	$msg = "\nTRACE:\n--------------------------\n";

	$backtrace = debug_backtrace();
	foreach ($backtrace as $key => $trace) {
		if ($key != 0) {
			$msg .= "#$key ";
			if (array_key_exists('file', $trace)) {
				$msg .= $trace['file'];
			}
			if (array_key_exists('line', $trace)) {
				$msg .= ":$trace[line]";
			}
			if (array_key_exists('class', $trace)) {
				$msg .= ":$trace[class]";
			}
			if (array_key_exists('type', $trace)) {
				$msg .= " $trace[type] " ; // ->, ::, or nothing
			}
			if (array_key_exists('function', $trace)) {
				$msg .= " $trace[function](";
				if (array_key_exists('args', $trace)) {
					$first = true;
					foreach ($trace['args'] as $arg) {
						if (!$first) {
							$msg .= ', ';
						}
						if (is_object($arg))
							$msg .= get_class($arg) . ' OBJECT ';
						else {
							if (is_string($arg)) {
								$msg .= $arg;
							}
							else {
								$msg .= 'TYPE: ' . gettype($arg);
							}
						}
						if ($first)
							$first = false;
					}
				}	
				$msg .= ')';
			}
			$msg .= "\n";
		}
	}
	$msg .= "\n\n";

	return $msg;

}

function fatal($pgmr_message, $user_message)
{
	error_log("DATE: " . date('c') . "\n", 3, 'error.log');
	error_log($pgmr_message, 3, 'error.log');
	error_log(trace());
	die($user_message);
	return false;
}

error_reporting (E_ALL ^ E_NOTICE);

/**
 * Error handler
 *
 * The error handler is used by all the code and parameters are filled automatically
 * by PHP. It may generate a user-visible error message, depending on the severity of
 * the error. In any case, it generates an error message to the error.log file, giving
 * a much more complete backtrace of the error.
 *
 * @param int $errno One of the PHP E_ error defines
 * @param string $errstr User supplied error string
 * @param string $errfile The offending filename
 * @param int $errline The line where the error occurred
 */

if (!function_exists('error_handler')) {
	function error_handler($errno, $errstr, $errfile, $errline)
	{
		// Set up array of PHP error codes and descriptive text
		$emsgs = array(E_ERROR => "FATAL ERROR",
			E_WARNING => "WARNING",
			E_PARSE => "PARSE_ERROR",
			E_NOTICE => "NOTICE",
			E_CORE_ERROR => "CORE PHP ERROR",
			E_CORE_WARNING => "CORE PHP WARNING",
			E_COMPILE_ERROR => "FATAL COMPILE ERROR",
			E_COMPILE_WARNING => "COMPILE WARNING",
			E_USER_ERROR => "USER ERROR",
			E_USER_WARNING => "USER WARNING",
			E_USER_NOTICE => "USER NOTICE",
			E_STRICT => 'PHP STRICT NOTICE');
		if (phpversion() >= '5.2.0')
			$emsgs[E_RECOVERABLE_ERROR] = 'RECOVERABLE ERROR';
		if (phpversion() >= '5.3.0')
			$emsgs[E_DEPRECATED] = 'DEPRECATED';
		if (phpversion() >= '5.3.0')
			$emsgs[E_USER_DEPRECATED] = 'DEPRECATED';


		// Handle the programmer first.

		$msg = "\nType: " . $emsgs[$errno] . "\n";
		$datestr = date('r');
		$msg .= "Timestamp: $datestr\n";
		$msg .= "Details: $errstr\n";
		$msg .= "File: $errfile\n";
		$msg .= "Line: $errline\n";

		error_log($msg, 3, 'error.log');
		error_log(trace(), 3, 'error.log');

		// Handle the user second

		die($emsgs[$errno] . ": $errstr");

		// Needed according to note http://us2.php.net/manual/en/function.set-error-handler.php
		// Allows PHP's error handler to proceed from here.
		// Also, for PHP >= v5.2 necessary to populate $php_errormsg.
	
		return false;
	}
}

set_error_handler('error_handler');

