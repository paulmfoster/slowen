<?php

// NOTE: After transaction entry, determine if further action (e.g. splits)
// needs to be done.

include 'init.php';

load('memory');
memory::merge($_POST);

if ($_POST['status'] == 'V') {
    memory::set('amount', 0);
}
elseif (!empty($_POST['cr_amount'])) {
    memory::set('amount', $_POST['cr_amount']);
}
elseif (!empty($_POST['dr_amount'])) {
    memory::set('amount', - $_POST['dr_amount']);
}
else {
    memory::set('amount', 0);
}

$split = $_POST['split'] ?? 0;
if ($split == 1) {
    redirect('splittxn.php?max_splits=' . $_POST['max_splits']);
}
else {
    $_POST['max_splits'] = 0;
    memory::merge($_POST);
    redirect('vrfytxn.php');
}
