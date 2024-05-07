<?php

include 'init.php';

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

