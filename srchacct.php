<?php

include 'init.php';

$account = model('account', $db);
global $acct_types;

$categories = $account->get_accounts();

$cat_options = array();
foreach ($categories as $cat) {
    $cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
        'val' => $cat['id']);
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

$focus_field = 'category';
$page_title = 'Search By Category/Account';
$return = 'srchacct2.php';

include VIEWDIR . 'acctsrch.view.php';

