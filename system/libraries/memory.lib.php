<?php

/**
 * memory class
 *
 * This class is used to store variables which must persist between page
 * calls. It's complicated to store these things in "hidden" fields.
 * The only effective way to store them is in the SESSION variable. But
 * we also need to be able to access them in a simpler way. Thus, this
 * class includes an array of stored variables.
 */

class memory
{
	static function set($key, $value)
	{
		$_SESSION['saved'][$key] = $value;
	}

	static function merge($data)
	{
		if (!isset($_SESSION['saved'])) {
			$_SESSION['saved'] = [];
		}
		$_SESSION['saved'] = array_merge($_SESSION['saved'], $data);
	}

	static function get($key)
	{
		return $_SESSION['saved'][$key] ?? NULL;
	}

	static function get_all()
	{
        if (!isset($_SESSION['saved'])) {
            return NULL;
        }
		return $_SESSION['saved'];
	}

	static function clear()
	{
		unset($_SESSION['saved']);
	}

	static function version()
	{
		return 2.0;
	}

}

