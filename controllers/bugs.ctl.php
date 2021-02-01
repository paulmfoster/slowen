<?php

class bugs_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();
		$this->nav->init('links.php');
	}

	function index()
	{
		$fields = [
			'app_title' => [
				'name' => 'app_title',
				'type' => 'hidden',
				'value' => $this->cfg['app_name']
			],
			'name' => [
				'name' => 'name',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40
			],
			'email' => [
				'name' => 'email',
				'type' => 'text',
				'required' => 1,
				'size' => 40,
				'maxlength' => 40
			],
			'remark' => [
				'name' => 'remark',
				'type' => 'textarea',
				'required' => 1,
				'rows' => 20,
				'cols' => 50,
				'size' => 1024
			],
			's1' => [
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Submit'
			]
		];
		$this->form->set($fields);
		$this->output('Bug Report/Feature Request', 'bugs');
	}

	function send()
	{
		$msg = 'Application: ' . $this->cfg['app_name'] . "\n";
		$msg .= 'Name: ' . $_POST['name'] . "\n";
		$msg .= 'Email: ' . $_POST['email'] . "\n\n";
		$msg .= 'Remarks: ' . $_POST['remark'] . "\n\n";

		mail('paulf@quillandmouse.com', 'Bug Report or Feature Request for ' . $this->cfg['app_name'] , $msg);
		emsg('S', 'Thanks for your feedback. It is appreciated.');

		relocate('index.php');
	}
}
