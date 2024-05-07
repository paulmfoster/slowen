<?php

include 'init.php';

$edit = $_POST['edit'] ?? NULL;
$delete = $_POST['delete'] ?? NULL;
$show = $_POST['show'] ?? NULL;
if (!is_null($show)) {
    redirect('showacct.php?id=' . $_POST['id']);
}
elseif (!is_null($edit)) {
    redirect('editacct.php?id=' . $_POST['id']);
}
elseif (!is_null($delete)) {
    redirect('delacct.php?id=' . $_POST['id']);
}
else {
    redirect('listacct.php');
}
