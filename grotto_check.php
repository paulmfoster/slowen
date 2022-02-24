<?php

/* =========== GROTTO CODE CHECK ============= */

if (!file_exists($cfg['grottodir'])) {
	echo "This software relies on another package called 'grotto', and I can't find<br/>";
	echo 'it on your system. It should be available from where you got this software.<br/>';
	echo 'Create a "grotto/" directory, download the grotto package and install it there.';
	die();
}

/* ========== END GROTTO CODE CHECK =========== */

