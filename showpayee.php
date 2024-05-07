<?php

include 'init.php';

$id = $_GET['id'] ?? NULL;
if (is_null($id))
    redirect('listpayee.php');

$p = model('payee', $db);

$payee = $p->get_payee($id);
$page_title = 'Show Payee';

include VIEWDIR . 'payshow.view.php';

