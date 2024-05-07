<?php

include 'init.php';

fork('s1', 'P', 'actsched.php');

$sched = model('scheduled', $db);

$howmany = $sched->activate_scheduled($_POST);
if ($howmany) {
    emsg('S', 'Scheduled transactions activated.');
}
else {
    emsg('F', 'No transactions were scheduled for activation.');
}

redirect('listsched.php');

