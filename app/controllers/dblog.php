<?php

class dblog extends controller
{
    public $cfg, $form, $nav, $db, $trans;
    public $page_title, $return, $mlog;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->mlog = model('log', $this->db);
    }

    // show the database log
    function index()
    {
        $recs = $this->mlog->getrecs();

        $this->page_title = 'View Database Log';
        $this->view('logview.view.php', ['recs' => $recs]);
        
    }

    function purge()
    {
        $this->mlog->purge();
        emsg('S', 'Database log purge completed.');
        redirect('index.php?c=dblog&m=index');
    }

}

