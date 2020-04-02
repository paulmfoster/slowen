<?php

// This file can be called at the end of every controller
// to engage the view. You must define the $base_dir
// and $view_file, plus other variables needed by the
// head and footer files

$views = array();
$views[] = $base_dir . 'views/head.view.php';
$views[] = $base_dir . $view_file;
$views[] = $base_dir . 'views/footer.view.php';

foreach ($views as $view) {
	include $view;
}

