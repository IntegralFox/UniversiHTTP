<?php

if ($_SESSION['faculty'] == 1 && $argc == 2 && $argv[1] == 'create') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$db = pdoConn();

		$firstName  = $_POST['firstName'];
		$middleName = empty($_POST['middleName']) ? null : $_POST['middleName'];
		$lastName   = $_POST['lastName'];
		$faculty    = isset($_POST['faculty']) ? 1 : 0;

		$username = $firstName[0];
		if (!empty($middleName)) $username .= $middleName[0];
		$username .= $lastName;
		$username = strtolower($username);

		$query = 'SELECT count(user_id)
			FROM user
			WHERE user_login like concat(:user, \'%\')';

		$userStmt = $db->prepare($query);
		$userStmt->bindParam(':user', $username, PDO::PARAM_STR);
		$userStmt->execute();
		$usernameCount = $userStmt->fetchColumn();
		$userStmt = null;

		if ($usernameCount > 0) $username .= $usernameCount;

		$password = substr(md5($username), 0, 10);
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$query = 'INSERT INTO user (user_name_last, user_name_first, user_name_middle, user_login, user_password, user_faculty, user_temp_password)
			VALUES (:last, :first, :middle, :login, :password, :faculty, 1)';

		$insertStmt = $db->prepare($query);
		$insertStmt->bindParam(':last', $lastName, PDO::PARAM_STR);
		$insertStmt->bindParam(':first', $firstName, PDO::PARAM_STR);
		$insertStmt->bindParam(':middle', $middleName, PDO::PARAM_STR);
		$insertStmt->bindParam(':login', $username, PDO::PARAM_STR);
		$insertStmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
		$insertStmt->bindParam(':faculty', $faculty, PDO::PARAM_INT);
		$insertStmt->execute();
		$insertStmt = null;

		$db = null;

		$template['createdAccounts'] = [[
			'name'     => "$lastName, $firstName $middleName",
			'login'    => $username,
			'password' => $password
		]];

		require('../template/accountResults.php');
	} else {
		require('../template/accountCreate.php');
	}
} else if ($_SESSION['faculty'] == 1 && $argc == 3 && $argv[1] == 'create' && $argv[2] == 'multiple') {
	$db = pdoConn();

	$query = 'INSERT INTO user (user_name_last, user_name_first, user_name_middle, user_login, user_password, user_faculty)
		VALUES (:last, :first, :middle, :login, :password, 0)';

	$insertStmt = $db->prepare($query);
	$insertStmt->bindParam(':last', $lastName, PDO::PARAM_STR);
	$insertStmt->bindParam(':first', $firstName, PDO::PARAM_STR);
	$insertStmt->bindParam(':middle', $middleName, PDO::PARAM_STR);
	$insertStmt->bindParam(':login', $username, PDO::PARAM_STR);
	$insertStmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);

	$query = 'SELECT count(user_id)
		FROM user
		WHERE user_login like concat(:user, \'%\')';

	$userStmt = $db->prepare($query);
	$userStmt->bindParam(':user', $username, PDO::PARAM_STR);

	$csv = file_get_contents($_FILES['csv']['tmp_name']);
	$csv = explode(PHP_EOL, $csv);
	if (isset($_POST['lineOneHeader'])) array_shift($csv);

	$template['createdAccounts'] = [];

	foreach ($csv as $line) {
		$line = explode(',', $line);
		if (empty($line[0]) || empty($line[1])) continue;

		$firstName  = trim($line[0]);
		$middleName = empty($line[2]) ? null : trim($line[2]);
		$lastName   = trim($line[1]);

		$username = $firstName[0];
		if (!empty($middleName)) $username .= $middleName[0];
		$username .= $lastName;
		$username = strtolower($username);

		$userStmt->execute();
		$usernameCount = $userStmt->fetchColumn();
		$userStmt->closeCursor();
		if ($usernameCount > 0) $username .= $usernameCount;

		$password = substr(md5($username), 0, 10);
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$insertStmt->execute();

		$template['createdAccounts'][] = [
			'name'     => "$lastName, $firstName $middleName",
			'login'    => $username,
			'password' => $password
		];
	}

	$insertStmt = null;
	$userStmt = null;

	$db = null;

	require('../template/accountResults.php');
} else if ($_SESSION['faculty'] == 1 && $argc == 2 && $argv[1] == 'edit') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['delete'])) {
			$db = pdoConn();
			$query = 'DELETE FROM user
				WHERE user_id = :user';

			$deleteStmt = $db->prepare($query);
			$deleteStmt->bindParam(':user', $userId, PDO::PARAM_INT);
			foreach ($_POST['courseEnrolled'] as $userId) {
				$deleteStmt->execute();
			}
			$deleteStmt = null;

			$db = null;

			header('Location: /account/edit');
		} else {
			$db = pdoConn();

			$query = 'SELECT user_name_last, user_name_first, user_name_middle, user_login
				FROM user
				WHERE user_id = :user';

			$userStmt = $db->prepare($query);
			$userStmt->bindParam(':user', $userId, PDO::PARAM_INT);

			$query = 'UPDATE user
				SET user_password = :password
				WHERE user_id = :user';

			$updateStmt = $db->prepare($query);
			$updateStmt->bindParam(':user', $userId, PDO::PARAM_INT);
			$updateStmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);

			$template['createdAccounts'] = [];

			foreach ($_POST['courseEnrolled'] as $userId) {
				$userStmt->execute();
				$userStmt->bindColumn('user_name_last', $lastName, PDO::PARAM_STR);
				$userStmt->bindColumn('user_name_first', $firstName, PDO::PARAM_STR);
				$userStmt->bindColumn('user_name_middle', $middleName, PDO::PARAM_STR);
				$userStmt->bindColumn('user_login', $username, PDO::PARAM_STR);
				$userStmt->fetch(PDO::FETCH_BOUND);
				$userStmt->closeCursor();

				$password = substr(md5($username), 0, 10);
				$passwordHash = password_hash($password, PASSWORD_DEFAULT);

				$updateStmt->execute();

				$template['createdAccounts'][] = [
					'name'     => "$lastName, $firstName $middleName",
					'login'    => $username,
					'password' => $password
				];
			}

			$updateStmt = null;
			$userStmt = null;

			$db = null;

			$template['reset'] = true;

			require('../template/accountResults.php');
		}
	} else {
		$db = pdoConn();

		$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
			FROM user
			WHERE user_faculty = 0';

		$studentStmt = $db->prepare($query);
		$studentStmt->execute();
		$template['student'] = $studentStmt->fetchAll(PDO::FETCH_ASSOC);
		$studentStmt = null;

		$db = null;

		require('../template/accountList.php');
	}
}

?>
