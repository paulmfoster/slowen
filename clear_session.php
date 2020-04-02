<?php

session_name('slowen');
session_start();

echo 'Before clearing...';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// unset($_SESSION);

$_SESSION['entity_num'] = 1;
$_SESSION['entity_name'] = 'Personal';

$x = session_destroy();

if ($x) {
	echo 'Successful...';
}
else {
	echo 'Failed...';
}

echo 'After clearing...';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

die('bravo');

header('Location: index.php');
exit();


