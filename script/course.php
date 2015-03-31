<?php

/* Handle course-listing homepage, course pages, and course creation */
if ($argc == 2 && $argv[1] == 'create') {
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

			$db = null;

			header('Location: /');
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

	print_r($template['course']);

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
