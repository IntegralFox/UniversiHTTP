<?php

/* Allows new users to set their password */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST['password']) && !empty($_POST['passwordConfirm'])
		&& $_POST['password'] == $_POST['passwordConfirm']) {
		$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

		$db = pdoConn();

		$query = 'UPDATE user
			SET user_password = :hash,
				user_temp_password = 0
			WHERE user_id = :user';

		$passwordStmt = $db->prepare($query);
		$passwordStmt->bindParam(':hash', $hash, PDO::PARAM_STR);
		$passwordStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
		$passwordStmt->execute();
		$passwordStmt = null;

		$db = null;

		$_SESSION['needsNewPassword'] = 0;

		header('Location: /');
		exit();
	} else {
		$template = ['error' => 'Passwords must match.'];
		require('../template/password.php');
		exit();
	}
} else {
	require('../template/password.php');
	exit();
}
