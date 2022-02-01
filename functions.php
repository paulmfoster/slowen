<?php

/**
 * instrument()
 *
 * Used in debugging. It shows the type and value of any variable.
 *
 * @param string $label What do you want to label this?
 * @param mixed $var What do you want to see?
 *
 */

function instrument($label, $var)
{
	echo $label . PHP_EOL;
	echo '<pre>' . PHP_EOL;
	print_r($var);
	echo '</pre>' . PHP_EOL;
}

function model($name)
{
	global $cfg, $db;

	$filename = $cfg['modeldir'] . $name . '.mdl.php';
	if (!file_exists($filename)) {
		die("Model $name doesn't exist!");
	}
	require_once($filename);
	$obj = new $name($db);
	return $obj;
}

function library($name, $param = NULL)
{
	global $cfg;

	$filename = $cfg['libdir'] . $name . '.lib.php';
	if (!file_exists($filename)) {
		die("Library $name doesn't exist!");
	}
	require_once($filename);
	if (is_null($param)) {
		$obj = new $name();
	}
	else {
		$obj = new $name($param);
	}
	return $obj;
}

function view($page_title, $data, $return, $view_file, $focus_field = '')
{
	global $cfg, $nav, $form;

	extract($data);
	include $cfg['viewdir'] . 'head.view.php';
	include $cfg['viewdir'] . $view_file . '.view.php';
	include $cfg['viewdir'] . 'footer.view.php';
}

function fork($varname, $method, $failurl)
{
	if ($method == 'P') {
		$var = $_POST[$varname] ?? NULL;
	}
	elseif ($method == 'G') {
		$var = $_GET[$varname] ?? NULL;
	}
	if (is_null($var)) {
		header('Location: ' . $failurl);
		exit;
	}
	return $var;
}

function redirect($url)
{
	header("Location: $url");
	exit();
}

function make_tables($db)
{
	global $cfg;

	// add the tables
	$cfile = 'coldstart.' . $cfg['dbdriv'];
	if (!file_exists($cfile)) {
		die("Selected entity needs the file '$cfile' to start, and it's missing.");
	}
	$lines = file($cfile, FILE_IGNORE_NEW_LINES);
	foreach ($lines as $line) {
		$db->query($line);
	}
}

