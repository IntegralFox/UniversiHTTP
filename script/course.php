<?php

/* Handle course-listing homepage, course pages, and course creation */
if ($_SESSION['faculty'] == 1 && $argc == 3 && $argv[1] == 'edit') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['delete'])) {
			$db = pdoConn();

			$query = 'DELETE FROM course
				WHERE course_id = :course';

			$courseStmt = $db->prepare($query);
			$courseStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
			$courseStmt->execute();
			$courseStmt = null;

			$db = null;

			header('Location: /');
		} else {
			$db = pdoConn();

			$query = 'UPDATE course
				SET course_number = :number,
					course_title = :title,
					course_year = :year,
					term_id = :term
				WHERE course_id = :course';

			$courseStmt = $db->prepare($query);
			$courseStmt->bindParam(':number', $_POST['courseNumber'], PDO::PARAM_STR);
			$courseStmt->bindParam(':title', $_POST['courseTitle'], PDO::PARAM_STR);
			$courseStmt->bindParam(':year', $_POST['courseYear'], PDO::PARAM_INT);
			$courseStmt->bindParam(':term', $_POST['courseTerm'], PDO::PARAM_INT);
			$courseStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
			$courseStmt->execute();
			$courseStmt = null;

			$query = 'SELECT user_id
				FROM course_user_bridge
				WHERE course_id = :course';

			$userStmt = $db->prepare($query);
			$userStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
			$userStmt->execute();
			$previousRoster = $userStmt->fetchAll(PDO::FETCH_ASSOC);
			$userStmt = null;

			array_walk($previousRoster, function(&$value, $index) {
				$value = $value['user_id'];
			});
			$newRoster = $_POST['courseEnrolled'];
			$addRoster = array_diff($newRoster, $previousRoster);
			$removeRoster = array_diff($previousRoster, $newRoster);

			if (count($removeRoster)) {
				$query = 'DELETE FROM course_user_bridge
					WHERE course_id = :course
					AND user_id = :user';

				$bridgeStmt = $db->prepare($query);
				$bridgeStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
				$bridgeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
				foreach ($removeRoster as $userId) {
					$bridgeStmt->execute();
				}
				$bridgeStmt = null;
			}

			if (count($addRoster)) {
				$query = 'INSERT INTO course_user_bridge (course_id, user_id)
					VALUES (:course, :user)';

				$bridgeStmt = $db->prepare($query);
				$bridgeStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
				$bridgeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
				foreach ($addRoster as $userId) {
					$bridgeStmt->execute();
				}
				$bridgeStmt = null;
			}

			$db = null;

			header('Location: /course/' . $argv[2]);
		}
	} else {
		$db = pdoConn();

		$query = 'SELECT course_number, course_title, course_year, term_id
			FROM course
			WHERE course_id = :course';

		$courseStmt = $db->prepare($query);
		$courseStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
		$courseStmt->execute();
		$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
		$courseStmt = null;

		$query = 'SELECT term_id, term_name
			FROM term
			ORDER BY term_id';

		$termStmt = $db->prepare($query);
		$termStmt->execute();
		$template['term'] = $termStmt->fetchAll(PDO::FETCH_ASSOC);
		$termStmt = null;

		$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
			FROM user
			INNER JOIN course_user_bridge USING (user_id)
			WHERE user_faculty = 0
			AND course_id = :course';

		$studentStmt = $db->prepare($query);
		$studentStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
		$studentStmt->execute();
		$template['enrolled'] = $studentStmt->fetchAll(PDO::FETCH_ASSOC);
		$studentStmt = null;

		$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
			FROM user
			WHERE user_faculty = 0
			AND user_id NOT IN (SELECT user_id FROM course_user_bridge WHERE course_id = :course)';

		$studentStmt = $db->prepare($query);
		$studentStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
		$studentStmt->execute();
		$template['student'] = $studentStmt->fetchAll(PDO::FETCH_ASSOC);
		$studentStmt = null;

		$db = null;

		$year = [];
		$now = new DateTime();
		$year[] = $now->format('Y');
		for ($i = 1; $i < 3; ++$i) {
			$year[] = $now->add(new DateInterval('P1Y'))->format('Y');
		}
		$template['year'] = $year;
		$template['editing'] = true;

		require('../template/courseCreate.php');
	}
} else if ($_SESSION['faculty'] == 1 && $argc == 2 && $argv[1] == 'create') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (
			!empty($_POST['courseNumber']) && !empty($_POST['courseTitle']) &&
			!empty($_POST['courseYear']) && !empty($_POST['courseTerm'])
		) {
			$db = pdoConn();

			$query = 'INSERT INTO course (course_number, course_title, course_year, term_id, user_id)
				VALUES (:number, :title, :year, :term, :user)';

			$courseStmt = $db->prepare($query);
			$courseStmt->bindParam(':number', $_POST['courseNumber'], PDO::PARAM_STR);
			$courseStmt->bindParam(':title', $_POST['courseTitle'], PDO::PARAM_STR);
			$courseStmt->bindParam(':year', $_POST['courseYear'], PDO::PARAM_INT);
			$courseStmt->bindParam(':term', $_POST['courseTerm'], PDO::PARAM_INT);
			$courseStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
			$courseStmt->execute();
			$courseStmt = null;

			$courseId = $db->lastInsertId();

			$query = 'INSERT INTO course_user_bridge (course_id, user_id)
				VALUES (:course, :user)';

			$bridgeStmt = $db->prepare($query);
			$bridgeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
			$bridgeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
			foreach ($_POST['courseEnrolled'] as $userId) {
				$bridgeStmt->execute();
			}
			$bridgeStmt = null;

			$db = null;

			header('Location: /course/' . $courseId);
		}
	} else {
		$db = pdoConn();

		$query = 'SELECT term_id, term_name
			FROM term
			ORDER BY term_id';

		$termStmt = $db->prepare($query);
		$termStmt->execute();
		$template['term'] = $termStmt->fetchAll(PDO::FETCH_ASSOC);
		$termStmt = null;

		$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
			FROM user
			WHERE user_faculty = 0';

		$studentStmt = $db->prepare($query);
		$studentStmt->execute();
		$template['student'] = $studentStmt->fetchAll(PDO::FETCH_ASSOC);
		$studentStmt = null;

		$db = null;

		$year = [];
		$now = new DateTime();
		$year[] = $now->format('Y');
		for ($i = 1; $i < 3; ++$i) {
			$year[] = $now->add(new DateInterval('P1Y'))->format('Y');
		}
		$template['year'] = $year;

		require('../template/courseCreate.php');
	}
} else if ($argc == 2) {
	$courseId = $argv[1];

	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title, user_name_last, user_name_first
		FROM course
		INNER JOIN user USING (user_id)
		WHERE course_id = :courseId';

	$courseStmt = $db->prepare($query);
	$courseStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
	$courseStmt->execute();
	$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$query = 'SELECT assignment_id, assignment_name, assignment_due, assignment_points
		FROM assignment
		WHERE course_id = :courseId
		ORDER BY assignment_due DESC';

	$assignStmt = $db->prepare($query);
	$assignStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
	$assignStmt->execute();
	$template['assignment'] = $assignStmt->fetchAll(PDO::FETCH_ASSOC);
	$assignStmt = null;

	$db = null;

	require('../template/courseView.php');
} else {
	// No argument to look up a course. Display the homepage.
	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title
		FROM course ';
	if ($_SESSION['faculty']) {
		$query .= 'WHERE user_id = :userId';
	} else {
		$query .= 'INNER JOIN course_user_bridge AS c USING (course_id)
			WHERE c.user_id = :userId';
	}

	$courseStmt = $db->prepare($query);
	$courseStmt->bindParam(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$courseStmt->execute();
	$template['course'] = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$db = null;

	require('../template/courseList.php');
}

?>
