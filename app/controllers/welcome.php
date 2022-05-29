<?php

class welcome extends controller
{
    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
    }

    function index()
    {
        $this->page_title = 'Home';
        $this->view('index.view.php');
    }

    function history()
    {
        $this->page_title = 'The History Of Slowen';
        $this->view('history.view.php');
    }

}
