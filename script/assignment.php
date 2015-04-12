<?php

/* Handle assignment creation, editing, and viewing */

if ($_SESSION['faculty'] == 1 && $argc == 3 && $argv[1] == 'edit') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['delete'])) {
			$db = pdoConn();

			$query = 'SELECT course_id
				FROM assignment
				WHERE assignment_id = :assignment';

			$courseStmt = $db->prepare($query);
			$courseStmt->bindParam(':assignment', $argv[2], PDO::PARAM_INT);
			$courseStmt->execute();
			$courseId = $courseStmt->fetchColumn(0);
			$courseStmt = null;

			$query = 'DELETE FROM assignment
				WHERE assignment_id = :assignment';

			$assignmentStmt = $db->prepare($query);
			$assignmentStmt->bindParam(':assignment', $argv[2], PDO::PARAM_INT);
			$assignmentStmt->execute();
			$assignmentStmt = null;

			$db = null;

			header('Location: /course/' . $courseId);
		} else {
			$_POST['assignmentDueDate'] = localToUTC($_POST['assignmentDueDate']);

			$db = pdoConn();

			$query = 'UPDATE assignment
				SET assignment_name = :name,
					assignment_description = :description,
					assignment_due = :due,
					assignment_points = :points
				WHERE assignment_id = :assignment';

			$assignmentStmt = $db->prepare($query);
			$assignmentStmt->bindParam(':name', $_POST['assignmentName'], PDO::PARAM_STR);
			$assignmentStmt->bindParam(':description', $_POST['assignmentDescription'], PDO::PARAM_STR);
			$assignmentStmt->bindParam(':due', $_POST['assignmentDueDate'], PDO::PARAM_STR);
			$assignmentStmt->bindParam(':points', $_POST['assignmentPoints'], PDO::PARAM_INT);
			$assignmentStmt->bindParam(':assignment', $argv[2], PDO::PARAM_INT);
			$assignmentStmt->execute();
			$assignmentStmt = null;

			$db = null;

			header('Location: /assignment/' . $argv[2]);
		}
	} else {
		$db = pdoConn();

		$query = 'SELECT course_number, course_title
			FROM course
			INNER JOIN assignment USING (course_id)
			WHERE assignment_id = :assignment';

		$courseStmt = $db->prepare($query);
		$courseStmt->bindParam(':assignment', $argv[2], PDO::PARAM_INT);
		$courseStmt->execute();
		$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
		$courseStmt = null;

		$query = 'SELECT assignment_name, assignment_description,
				assignment_due, assignment_points
			FROM assignment
			WHERE assignment_id = :assignment';

		$assignmentStmt = $db->prepare($query);
		$assignmentStmt->bindParam(':assignment', $argv[2], PDO::PARAM_INT);
		$assignmentStmt->execute();
		$template['assignment'] = $assignmentStmt->fetch(PDO::FETCH_ASSOC);
		$assignmentStmt = null;

		$db = null;

		$template['assignment']['assignment_due'] = utcToLocal($template['assignment']['assignment_due']);
		$template['editing'] = true;

		require('../template/assignmentCreate.php');
	}
} else if ($_SESSION['faculty'] == 1 && $argc == 3 && $argv[1] == 'create') {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (
			!empty($_POST['assignmentName']) && !empty($_POST['assignmentDescription']) &&
			!empty($_POST['assignmentDueDate']) && !empty($_POST['assignmentPoints'])
		) {
			$_POST['assignmentDueDate'] = localToUTC($_POST['assignmentDueDate']);

			$db = pdoConn();

			$query = 'INSERT INTO assignment (assignment_name, assignment_description,
					assignment_due, assignment_points, course_id)
				VALUES (:name, :description, :due, :points, :course)';

			$courseStmt = $db->prepare($query);
			$courseStmt->bindParam(':name', $_POST['assignmentName'], PDO::PARAM_STR);
			$courseStmt->bindParam(':description', $_POST['assignmentDescription'], PDO::PARAM_STR);
			$courseStmt->bindParam(':due', $_POST['assignmentDueDate'], PDO::PARAM_STR);
			$courseStmt->bindParam(':points', $_POST['assignmentPoints'], PDO::PARAM_INT);
			$courseStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
			$courseStmt->execute();
			$courseStmt = null;

			$assignmentId = $db->lastInsertId();

			$db = null;

			header('Location: /assignment/' . $assignmentId);
		}
	} else {
		$db = pdoConn();

		$query = 'SELECT course_number, course_title
			FROM course
			WHERE course_id = :course';

		$courseStmt = $db->prepare($query);
		$courseStmt->bindParam(':course', $argv[2], PDO::PARAM_INT);
		$courseStmt->execute();
		$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
		$courseStmt = null;

		$db = null;

		require('../template/assignmentCreate.php');
	}
} else if ($_SESSION['faculty'] == 1 && $argc == 4 && $argv[2] == 'user') {
	$assignmentId = $argv[1];
	$userId = $argv[3];

	$db = pdoConn();

	$query = 'SELECT assignment_id, assignment_name, assignment_due, assignment_points
		FROM assignment
		WHERE assignment_id = :assignment';

	$assignmentStmt = $db->prepare($query);
	$assignmentStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$assignmentStmt->execute();
	$template['assignment'] = $assignmentStmt->fetch(PDO::FETCH_ASSOC);
	$assignmentStmt = null;

	$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
		FROM user
		WHERE user_id = :user';

	$userStmt = $db->prepare($query);
	$userStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$userStmt->execute();
	$template['user'] = $userStmt->fetch(PDO::FETCH_ASSOC);
	$userStmt = null;

	$query = 'SELECT grade_points, grade_comment
		FROM grade
		WHERE assignment_id = :assignment
		AND user_id = :user';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['grade'] = $gradeStmt->fetch(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$db = null;

	require('../template/assignmentDetailViewFaculty.php');
} else if ($_SESSION['faculty'] == 1 && $argc == 2 && $argv[1] == 'grade') {
	$assignmentId = $_POST['assignment'];
	$userId = $_POST['user'];
	$grade = $_POST['grade'];
	$comment = $_POST['comment'];

	$db = pdoConn();

	$query = 'SELECT count(grade_points)
		FROM grade
		WHERE assignment_id = :assignment
		AND user_id = :user';

	$gradedStmt = $db->prepare($query);
	$gradedStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$gradedStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradedStmt->execute();
	$graded = $gradedStmt->fetchColumn();
	$gradedStmt = null;

	if ($graded) {
		$query = 'UPDATE grade
			SET grade_points = :grade,
				grade_comment = :comment
			WHERE assignment_id = :assignment
			AND user_id = :user';

		$updateStmt = $db->prepare($query);
		$updateStmt->bindParam(':grade', $grade, PDO::PARAM_INT);
		$updateStmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$updateStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$updateStmt->bindParam(':user', $userId, PDO::PARAM_INT);
		$updateStmt->execute();
		$updateStmt = null;
	} else {
		$query = 'INSERT INTO grade (assignment_id, user_id, grade_points, grade_comment)
			VALUES (:assignment, :user, :grade, :comment)';

		$insertStmt = $db->prepare($query);
		$insertStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$insertStmt->bindParam(':user', $userId, PDO::PARAM_INT);
		$insertStmt->bindParam(':grade', $grade, PDO::PARAM_INT);
		$insertStmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$insertStmt->execute();
		$insertStmt = null;
	}
} else if ($argc == 2) {
	$assignmentId = $argv[1];
	$db = pdoConn();

	$query = 'SELECT assignment_id, assignment_name, assignment_description,
			assignment_due, assignment_points
		FROM assignment
		WHERE assignment_id = :assignment';

	$assignmentStmt = $db->prepare($query);
	$assignmentStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$assignmentStmt->execute();
	$template['assignment'] = $assignmentStmt->fetch(PDO::FETCH_ASSOC);
	$assignmentStmt = null;

	if ($_SESSION['faculty']) {
		$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle,
				COUNT(file_id) AS file_count, MAX(file_modified) AS file_modified,
				TIMESTAMPDIFF(HOUR, assignment_due, file_modified) AS file_hours,
				file_modified > assignment_due AS file_overdue, grade_points
			FROM user
			INNER JOIN course_user_bridge USING (user_id)
			INNER JOIN assignment USING (course_id)
			LEFT JOIN file USING (assignment_id, user_id)
			LEFT JOIN grade USING (assignment_id, user_id)
			WHERE assignment_id = :assignment
			GROUP BY user_id
			ORDER BY user_name_last ASC, user_name_first ASC, user_name_middle ASC';

		$submissionStmt = $db->prepare($query);
		$submissionStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$submissionStmt->execute();
		$template['submission'] = $submissionStmt->fetchAll(PDO::FETCH_ASSOC);
		$submissionStmt = null;
	} else {
		$query = 'SELECT grade_points, grade_comment
			FROM grade
			WHERE assignment_id = :assignment
			AND user_id = :user';

		$gradeStmt = $db->prepare($query);
		$gradeStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$gradeStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
		$gradeStmt->execute();
		$template['grade'] = $gradeStmt->fetch(PDO::FETCH_ASSOC);
		$gradeStmt = null;
	}

	$db = null;

	if ($_SESSION['faculty']) {
		require('../template/assignmentViewFaculty.php');
	} else {
		require('../template/assignmentViewStudent.php');
	}
} else {
	// No argument to look up a assignment. Display the homepage.
	header('Location: /');
}

?>
