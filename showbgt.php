<?php

include 'init.php';

$bg = model('budget', $db);
$cells = $bg->get_cells();

if ($cells === FALSE) {
    $totals = FALSE;
}
else {
    $wedate = $cells[0]['wedate'];
    $xwedate = new xdate;
    $xwedate->from_iso($wedate);
    $hr_wedate = $xwedate->to_amer();
    $totals = $bg->get_totals($cells);
}

$page_title = 'Budget List';
include VIEWDIR . 'showbgt.view.php';

