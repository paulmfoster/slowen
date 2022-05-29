<?php

class aud extends controller
{
    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->audit = model('audit', $this->db);
    }

    function monthly()
    {
        $month_options = array(
            array('lbl' => 'January', 'val' => 1),
            array('lbl' => 'February', 'val' => 2),
            array('lbl' => 'March', 'val' => 3),
            array('lbl' => 'April', 'val' => 4),
            array('lbl' => 'May', 'val' => 5),
            array('lbl' => 'June', 'val' => 6),
            array('lbl' => 'July', 'val' => 7),
            array('lbl' => 'August', 'val' => 8),
            array('lbl' => 'September', 'val' => 9),
            array('lbl' => 'October', 'val' => 10),
            array('lbl' => 'November', 'val' => 11),
            array('lbl' => 'December', 'val' => 12)
        );

        for ($i = 2016; $i < 2050; $i++) {
            $year_options[] = array('lbl' => $i, 'val' => $i);
        }

        $fields = array(
            'month' => array(
                'name' => 'month',
                'type' => 'select',
                'options' => $month_options
            ),
            'year' => array(
                'name' => 'year',
                'type' => 'select',
                'options' => $year_options
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Report'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Monthly Audit';
        $this->return = url('aud', 'show');
        $this->focus_field = 'month';
        $this->view('auditm.view.php');
    }

    function yearly()
    {
        for ($i = 2016; $i < 2050; $i++) {
            $year_options[] = array('lbl' => $i, 'val' => $i);
        }

        // $state == 0
        $fields = array(
            'year' => array(
                'name' => 'year',
                'type' => 'select',
                'options' => $year_options
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Report'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Yearly Audit';
        $this->focus_field = 'year';
        $this->return = url('aud', 'show');
        $this->view('audity.view.php');
    }

    function show()
    {
        $month = $_POST['month'] ?? NULL;
        $year = $_POST['year'] ?? NULL;

        if (is_null($year)) {
            redirect('index.php');
        }

        if (is_null($month)) {
            $data = $this->audit->yearly_audit($_POST['year']);
        }
        else {
            $data = $this->audit->monthly_audit($_POST['year'], $_POST['month']);
        }

        $print_filename = PRINTDIR . $data['filename'];
        $web_filename = PRINTDIR . $data['filename'];

        $this->audit->print_audit($data, $print_filename);

        $d = [
            'data' => $data,
            'web_filename' => $web_filename
        ];

        $this->page_title = 'Audit';
        $this->view('audshow.view.php', $d);

    }
}
