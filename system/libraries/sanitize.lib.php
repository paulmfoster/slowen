<?php

class sanitize
{
	static function nohtml($content)
	{
		$flags = FILTER_FLAG_STRIP_LOW |
			FILTER_FLAG_STRIP_HIGH;
		$ret = filter_var($content, $flags);
		$ret = strip_tags($ret);
		return $ret;
	}

	static function float($content)
	{
		return filter_var($content, FILTER_SANITIZE_NUMBER_FLOAT);
	}

	static function int($content)
	{
		return filter_var($content, FILTER_SANITIZE_NUMBER_INT);
	}

	static function url($content)
	{
		return filter_var($content, FILTER_SANITIZE_URL);
	}

	static function string($content)
	{
		return filter_var($content, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	}

	static function alphanum($content)
	{
		return preg_replace('/[^A-Za-z0-9]/', '', $content);
	}

	static function email($content)
	{
		return filter_var($content, FILTER_SANITIZE_EMAIL);
	}

	static function login($content)
	{
		return preg_replace('%[^0-9A-Za-z]%', '', $content);
	}

	static function name($content)
	{
		return preg_replace('%[^\. ,\-0-9A-Za-z]%', '', $content);
	}

	function version()
	{
		return 1.1;
	}

}

