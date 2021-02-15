<?php

// This file can be called at the end of every controller
// to engage the view. You must define the $base_dir
// and $view_file, plus other variables needed by the
// head and footer files

$views = array();
$views[] = $cfg['viewdir'] . 'head.view.php';
$views[] = $view_file;
$views[] = $cfg['viewdir'] . 'footer.view.php';

foreach ($views as $view) {
	include $view;
}

