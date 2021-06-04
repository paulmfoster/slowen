<?php
include 'init.php';
$s1 = fork('s1', 'P', 'entadd.php');
$ent = model('entity');
$ent->add_entity($_POST);
redirect('index.php');

