<?php

class welcome extends controller
{
    function __construct()
    {
        global $init;
        list($this->cfg, $this->form, $this->nav) = $init;
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
