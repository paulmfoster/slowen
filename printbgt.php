<?php

include 'init.php';

$bg = model('budget', $db);
$bg->print();
redirect('showbgt.php');

