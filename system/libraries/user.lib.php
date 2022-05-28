<?php

class user
{
    private const EVERYONE = 255;

	function __construct($db)
	{
        $this->db = $db;
        $this->make_tables();
        if (!$this->has_users()) {
            $this->sample_admin();
        }
	}

    function has_users()
    {
        $sql = "SELECT * FROM user";
        $u = $this->db->query($sql)->fetch();
        return ($u === FALSE) ? FALSE : TRUE;
    }

    function make_tables()
    {
        $sql = "CREATE TABLE IF NOT EXISTS user (id integer primary key autoincrement, login varchar(30), password varchar(255), name varchar(50) not null, email varchar(255) not null, nonce varchar(255) not null, level integer default 255)";
        $this->db->query($sql);
        $sql = "CREATE TABLE IF NOT EXISTS confirm (id integer primary key autoincrement, login varchar(30), password varchar(255), name varchar(50), email varchar(255), nonce varchar(255), level integer, ip varchar(15), link varchar(255), timestamp integer)";
        $this->db->query($sql);
    }

    function sample_admin()
    {
        $user = [
            'login' => 'admin',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'name' => 'Administrator',
            'email' => 'me@example.com',
            'nonce' => md5('admin'),
            'level' => 0
        ];
        $this->db->insert('user', $user);
    }

    function last_user()
    {
        $id = $this->db->lastid('user');
        $sql = "SELECT * FROM user WHERE id = $id";
        return $this->db->query($sql)->fetch();
    }

	/**
	 * get_ip_address()
	 *
	 * Should obtain and return IP address under almost all
	 * circumstances. Code is taken from stackoverflow.com, and tries to
	 * compensate for proxies, etc.
	 *
	 */

	private function get_ip_address()
	{
		$server_parms = [
			'HTTP_CLIENT_IP', 
			'HTTP_X_FORWARDED_FOR', 
			'HTTP_X_FORWARDED', 
			'HTTP_X_CLUSTER_CLIENT_IP', 
			'HTTP_FORWARDED_FOR', 
			'HTTP_FORWARDED', 
			'REMOTE_ADDR'
		];

		foreach ($server_parms as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					$ip = trim($ip); // just to be safe

					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
	}

	private function random_string($length = 32) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len = strlen($characters);
		$rs = '';
		for ($i = 0; $i < $length; $i++) {
			$rs .= $characters[rand(0, $len - 1)];
		}
		return $rs;
	}

	function register($post)
	{
		global $form;

		// did the user omit a required field?
		if (!$form->check_requireds($post)) {
			emsg('F', 'One or more required fields not provided');
			return FALSE;
		}

		// password and confirm must match
		if ($post['password'] !== $post['confirm']) {
			emsg('F', 'Password and confirmation do not match');
			return FALSE;
		}

		// hacker login?
		if ($post['login'] != preg_replace('%[^0-9A-Za-z]%', '', $content)) {
			emsg('F', 'Not-allowed characters in login');
			return FALSE;
		}

		// hacker name?
		if ($post['name'] != preg_replace('%[^\. ,\-0-9A-Za-z]%', '', $content)) {
			emsg('F', 'Not-allowed characters in user name');
			return FALSE;
		}

		// hacker email?
		if ($post['email'] != filter_var($content, FILTER_SANITIZE_EMAIL)) {
			emsg('F', 'Invalid email address');
			return FALSE;
		}
		
		// check for pre-existing identical login
		$sql = "SELECT * FROM user WHERE login = '{$post['login']}'";
		$pre_exist = $this->db->query($sql)->fetch();
		if ($pre_exist) {
			// user login already exists
			emsg('F', 'A user with this login already exists');
			return FALSE;
		}

		$save = array(
			'login' => $post['login'],
			'password' => password_hash($post['password'], PASSWORD_BCRYPT),
			'name' => $post['name'],
			'email' => $post['email'],
			'nonce' => md5($post['login']),
			'level' => 255,
			'ip' => $this->get_ip_address(),
			'link' => $this->random_string(32),
			'timestamp' => time()
		);
		$save_prepped = $this->db->prepare('confirm', $save);
		$this->db->insert('confirm', $save_prepped);

		$message = <<<EOD

In order to confirm your registration with our site you must copy
the "token" below on the web page you came from.

Token: {$save['link']}

Thanks!

EOD;

		$subject = 'Please CONFIRM your registration with our site';
		mail($post['email'], $subject, $message);

		return TRUE;
	}


	/**
	 * confirm_registration()
	 *
	 * Confirms (or not) that a user is allowed to register, and does
	 * so.
	 *
	 * @param string $get confirmation link
	 *
	 * @return boolean Either you're okay or you're not
	 *
	 */

	function confirm_registration($token)
	{
		// XSS
		$token = preg_replace('/[^A-Za-z0-9]/', '', $token);

		$now = time();
		$limit = $now - 3600;

		// best time to delete all the expired records
		$sql = "DELETE FROM confirm WHERE timestamp < $limit";
		$this->db->query($sql);

		// grab confirmation record
		$sql = "SELECT * FROM confirm WHERE link = '$token'";
		$rec = $this->db->query($sql)->fetch();

		// can't find the confirmation key
		if ($rec === FALSE) {
			emsg('F', 'No such confirmation token, or token expired');
			return FALSE;
		}

		$store = array(
			'login' => $rec['login'],
			'password' => $rec['password'],
			'name' => $rec['name'],
			'email' => $rec['email'],
			'nonce' => $rec['nonce'],
			'level' => $rec['level']
		);

		$save = $this->db->prepare('user', $store);
		$this->db->insert('user', $save);

		$sql = "DELETE FROM confirm WHERE link = '$token'";
		$this->db->query($sql);

		return TRUE;
	}

	function get_user_list()
	{
		$sql = "SELECT * FROM user ORDER BY name";
		return $this->db->query($sql)->fetch_all();
	}

	function get_user_by_nonce($nonce)
	{
		// XSS
		$nonce = preg_replace('/[^A-Za-z0-9]/', '', $nonce);

		$sql = "SELECT * FROM user WHERE nonce = '$nonce'";
		return $this->db->query($sql)->fetch();
	}

	function get_current_user()
	{
		if (isset($_SESSION['user'])) {
			$u = $this->get_user_by_nonce($_SESSION['user']);
			return $u;
		}
		return FALSE;
	}

	function get_user($id)
	{
		$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

		$sql = "SELECT * FROM user WHERE id = $id";
		return $this->db->query($sql)->fetch();
	}

	function get_user_by_login($login)
	{
		$sql = "SELECT * FROM user WHERE login = '$login'";
		return $this->db->query($sql)->fetch();
	}

	function get_admin_users()
	{
		$sql = "SELECT * FROM user WHERE level = 0";
		return $this->db->query($sql)->fetch_all();
	}

	function get_non_admin_users()
	{
		$sql = "SELECT * FROM user WHERE level != 0";
		return $this->db->query($sql)->fetch_all();
	}

	/**
	 * create_user()
	 *
	 * Does the heavy lifting of creating a user,
	 * after we figure out if it's okay for him
	 * to create that user.
	 *
	 * @param array $post The $_POST array
	 *
	 * @return boolean TRUE
	 *
	 */

	private function create_user($post)
	{
		// encrypt the password
		$post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
		$post['nonce'] = md5($post['login']);

		$add_array = $this->db->prepare('user', $post);
		$this->db->insert('user', $add_array);

		return TRUE;
	}

	function add_user($post)
	{
		// check for pre-existing identical login
		$sql = "SELECT * FROM user WHERE login = '{$post['login']}'";
		$pre_exist = $this->db->query($sql)->fetch();
		if ($pre_exist) {
			// user login already exists
			emsg('F', 'User login already exists');
			return FALSE;
		}

		// logged in?/determine level of user
		if (isset($_SESSION['user'])) {
			$sql = "SELECT level FROM user WHERE nonce = '{$_SESSION['user']}'";
			$level_rec = $this->db->query($sql)->fetch();
			$level = $level_rec['level'];
		}
		else {
			// signal value for "not logged in"
			$level = 1024;
		}

		if ($level == 0) {
			// admin, create any users
			$this->create_user($post);
			return TRUE;
		}
		elseif ($level == 255) {
			// regular user, can't create other users
			emsg('F', 'You are not authorized to create a new user.');
			return FALSE;
		}
		elseif ($level == 1024) {
			// not logged in
			if  ($post['level'] != 255) {
				// can't create an admin user
				emsg('F', 'You are not authorized to create an admin user.');
				return FALSE;
			}

			// NOTE Theoretically, a not-logged-in user could
			// create numerous low level users
	
			$this->create_user($post);
			return TRUE;
		}

	}

	function update_user($post)
	{
		$rec = [
			'name' => $post['name'],
			'email' => $post['email']
		];

        if (isset($post['level'])) {
            $rec['level'] = $post['level'];
        }

		// check for password change
		if (!empty($post['password'])) {
			// does password === confirm?
			if ($post['password'] === $post['confirm']) {
				// encrypt password for storage
				$post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
				$rec['password'] = $post['password'];
			}
			else {
				emsg('F', "Confirm password doesn't match original");
				return FALSE;
			}
			
		}

		$user = $this->db->prepare('user', $post);
		$this->db->update('user', $user, "id = {$user['id']}");

		return TRUE;
	}

	function delete_user($userid)
	{
		$userid = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);

		// id == 0 not a real user ID
		if ($userid == 0) {
			emsg('F', 'Cannot delete a non-existent user');
			return FALSE;
		}

		// all okay; delete user
		$sql = "DELETE FROM user WHERE id = $userid";
		$this->db->query($sql);
		return TRUE;
	}

	/**
	 * Log in.
	 *
	 * Checks the login and password from the login screen.
	 * Ensures the user in question exists, and that the
	 * password entered matches the one on file.
	 *
	 * @param array $post The $_POST array
	 *
	 * @return boolean TRUE if user found, else FALSE
	 *
	 */

	function login($post)
	{
		$sql = "SELECT * FROM user WHERE login = '{$post['login']}'";
		$user = $this->db->query($sql)->fetch();

		if ($user) {
			$verified = password_verify($post['password'], $user['password']);
			if ($verified) {
				// this tells the system who's logged in
				$_SESSION['user'] = $user['nonce'];
				return TRUE;
			}
			else {
				// password didn't match
				return FALSE;
			}
		}
		else {
			// no such user
			return FALSE;
		}
	}

	/**
	 * Limit access in a script.
	 *
	 * This function should appear in every controller which demands
	 * some privilege in order to access that page.
	 *
	 * @param int $level The level of user needed to access the page.
	 *
	 * @return boolean TRUE if the user is qualified, else FALSE
	 *
	 */

	function access($level)
	{
		$level = filter_var($level, FILTER_SANITIZE_NUMBER_INT);

		if (isset($_SESSION['user'])) {

			// user is logged in
			$sql = "SELECT level FROM user WHERE nonce = '{$_SESSION['user']}'";
			$user = $this->db->query($sql)->fetch();

			if ($user === FALSE) {
				// nonce is not in the system,
				// so user was deleted,
				// or someone's attempting a hack
				emsg('F', 'User is anonymous or not in the system.');
				return FALSE;
			}	
				
			if ($user['level'] > $level) {
				// user isn't qualified 
				emsg('F', 'User not authorized.');
				return FALSE;
			}
		}
		elseif ($level < self::EVERYONE) {
			// user is not logged in or no user
			// access level is "below" the "everyone" threshold
			emsg('F', 'User not logged in or no user');
			return FALSE;
		}

		return TRUE;
	}
	
	function fetch_user_info()
	{
		if (isset($_SESSION['user'])) {
			$sql = "SELECT * FROM user WHERE nonce = '{$_SESSION['user']}'";
			$details = $this->db->query($sql)->fetch();
			return $details;
		}
		else {
			return FALSE;
		}
	}

	function is_admin()
	{
		if (isset($_SESSION['user'])) {
			$sql = "SELECT level FROM user WHERE nonce = '{$_SESSION['user']}'";
			$level_rec = $this->db->query($sql)->fetch();
			if ($level_rec['level'] == 0) {
				return TRUE;
			}
		}
		return FALSE;
	}


};

