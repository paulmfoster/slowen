<?php

include 'init.php';
$rpt = model('report');

$categories = $rpt->get_accounts();

$cat_options = array();
foreach ($categories as $cat) {
	$cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
		'val' => $cat['acct_id']);
}

$fields = array(
	'category' => array(
		'name' => 'category',
		'type' => 'select',
		'options' => $cat_options
	),
	's1' => array(
		'name' => 's2',
		'type' => 'submit',
		'value' => 'Search'
	)
);
$form->set($fields);

view('Search By Category/Account', [], 'results.php', 'srchaccts');

