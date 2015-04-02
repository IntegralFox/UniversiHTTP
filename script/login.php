<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$template = [];
	require('../template/login.php');
	die();
} else {
	$username = isset($_POST['usernamephp']) ? $_POST['usernamephp'] : '';
	$password = isset($_POST['passwordphp']) ? $_POST['passwordphp'] : '';

	if (!empty($username) && !empty($password)) {
		$db = pdoConn();

		$query = 'SELECT user_id, user_password, user_name_last, user_name_first, user_faculty from user where user_login = :username';

		$return = $db -> prepare($query);
		$return -> bindParam(':username',$username, PDO::PARAM_STR);
		$return->execute();
		$returnArray = $return->fetch(PDO::FETCH_ASSOC);
		$return = null;

		$db = null;

		if (password_verify($password, $returnArray['user_password'])) {
			//putting the user id into the session
			$_SESSION['userId']    = $returnArray['user_id'];
			$_SESSION['firstName'] = $returnArray['user_name_first'];
			$_SESSION['lastName']  = $returnArray['user_name_last'];
			$_SESSION['faculty']   = $returnArray['user_faculty'];
			header('Location: ' . $_SERVER['REQUEST_URI']);
		} else {
			//	echo'Username or Password not found please use the back arrow to go back to the log in screen.';
			sleep(1);
			$template = [
				'username' => htmlentities($username),
				'error'    => 'Username / Password combination not found.'
			];
			require('../template/login.php');
			die();
		}
	} else {
		$template = [
			'username' => htmlentities($username),
			'error'    => 'Please enter a username and password.'
		];
		require('../template/login.php');
		die();
	}
}

?>
