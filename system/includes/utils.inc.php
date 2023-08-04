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
	// the following makes the output of print_r() actually readable in a
	// HTML page
	echo '<pre>' . PHP_EOL;
	print_r($var);
	echo '</pre>' . PHP_EOL;
}

/**
 * Load a library or an include file.
 *
 * Include a file. If it is a library, instantiate the library and
 * return the resultant object. Optionally, pass a parameter to its
 * constructor.
 *
 * @param string $name Basename of file (and class)
 * @param string $param Optional parameter
 *
 * @return object The object created, if any
 */

function load($name, $param = NULL)
{
	// try inc file first
	$filename = INCDIR . $name . '.inc.php';
	if (!file_exists($filename)) {
		// try lib file last
		$filename = LIBDIR . $name . '.lib.php';
		if (!file_exists($filename)) {
			die("File $name.inc.php or $name.lib.php do not exist!");
		}
        else {
            // a class/library file
			include $filename;
			if (is_null($param)) {
				$obj = new $name();
			}
			else {
				$obj = new $name($param);
			}
			return $obj;
		}
	}
	else {
        // a straight include file
		include $filename;
		return;
	}

}

/**
 * Instantiate a model
 *
 * We pass the basename of the model. The model name should be
 * <basename>.mdl.php, and the class should be the same name. Note: the
 * $cfg should be present and is globalized. The $db database connection
 * object is also globalized, and it's assumed that the model needs that
 * object passed to its constructor. If not, just provide a NULL value for
 * the $db object. The use of the $db object, and the directory it is in
 * are what separate models and libraries (q.v.).
 *
 * @param string Basename of the model
 * @param mixed Any parameters
 *
 * @return object Instantiated model object
 *
 */

function model($name, $params = NULL)
{
	$filename = MODELDIR . $name . '.php';
	if (!file_exists($filename)) {
		die("Model $name doesn't exist!");
	}
	include $filename;
    if (is_null($params)) {
	    $obj = new $name();
    }
    else {
        $obj = new $name($params);
    }
	return $obj;
}

/**
 * Assign/check variable or divert.
 *
 * Check for the existence of a variable via the method passed ('P' for
 * POST, 'G' for GET). If found, return the variable value. Otherwise,
 * divert to the passed URL.
 *
 * @param string $varname The variable to check for
 * @param string $method 'G' = GET, 'P' = POST
 * @param string $failurl Where to go on failure
 *
 * @return string Value of variable, if present
 */

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

/**
 * Are the required POST values present?
 *
 * This routine checks to see that the required values exist in the POST
 * array.
 *
 * @param array $post The POST array
 * @param array $indexes The POST indexes which should be present
 *
 * @return boolean TRUE if all found, FALSE otherwise
 */

function filled_out($post, $indexes)
{
	$errors = 0;
	foreach ($indexes as $key => $value) {
		if (! array_key_exists($value, $post)) {
			$errors++;
		}
		elseif (is_null($post[$value])) {
			$errors++;	
		}
        elseif (strlen(trim($post[$value])) == 0) {
            $errors++;
        }
	}

	return ($errors == 0) ? TRUE : FALSE;
}

function redirect($url)
{
	header("Location: $url");
	exit();
}

/** Create the tables for this application.
 *
 * Generally assumes that a database exists (the connection for which is
 * passed in as a parameter), but that no tables exist. This routine looks
 * for a file of SQL statements, one per line. This file should be a dump
 * of the structures of all the tables needed, in SQL format for the driver
 * involved. It then executes the SQL to create the tables.
 *
 * @param string $dsn The PDO DSN for the database 
 * @param string $sqlfile The SQL file to build the tables 
 * @return object Database object for use later
 */

function make_tables($dsn, $sqlfile)
{
	// add the tables
	if (!file_exists($sqlfile)) {
		die("You need the file '$sqlfile' to start, and it's missing.");
	}
    $db = load('database', $dsn);
	$lines = file($sqlfile, FILE_IGNORE_NEW_LINES);
	foreach ($lines as $line) {
		$db->query($line);
	}

    return $db;
}

/** Create the tables for this application.
 *
 * Generally assumes that a database exists (the connection for which is
 * passed in as a parameter), but that no tables exist. This routine looks
 * for a PHP file which contains an array of SQL "CREATE TABLE" statments
 * in an array called $schema.  file called 'coldstart.<driver_type>'. It
 * then executes the SQL to create the tables.
 *
 * @param string $dsn The PDO DSN for the database
 * @param string $sqlfile The SQL file to build the tables
 * @return object Database object for use later
 */

function generate_tables($dsn, $sqlfile)
{
	// add the tables
	if (!file_exists($sqlfile)) {
		die("You need the file '$sqlfile' to start, and it's missing.");
	}
    $db = load('database', $dsn);
    include $sqlfile;
	foreach ($schema as $sql) {
		$db->query($sql);
	}

    return $db;
}

/**
 * Generate/populate tables.
 *
 * Assumes you have a PHP file with an array of SQL statements. This
 * routine will run those statements one at a time to either
 * create/generate the tables needed or populate them.
 *
 * @param object $db A database object
 * @param string $sqlfile The PHP file with SQL statement array
 */

function genpop($db, $sqlfile)
{
	// add the tables
	if (!file_exists($sqlfile)) {
		die("You need the file '$sqlfile' to start, and it's missing.");
	}
    include $sqlfile;
	foreach ($records as $sql) {
		$db->query($sql);
	}
}

/** Simplify writing out URLs for user.
 *
 * Originally, Grotworx wanted URLs in the form of
 * "index.php?url=controller/method/params". This is cumbersome to write
 * out. And at some point, some "better" scheme might be used. This
 * function is designed to allow the user to skip the "index.php?url="
 * preamble, and just supply the controller, method and parameters. In
 * addition, if the "index.php?url=" part ever changes, it only has to be
 * changed in this function (and the router code) in order to make the
 * change global (assuming the user uses this function for his/her URLs).
 *
 * @return string The URL needed for the system.
 */

function url()
{
    $args = func_get_args();
    $str = implode('/', $args);
    return "index.php?url=$str";
}

