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
} else if ($argc == 2) {
	$db = pdoConn();

	$query = 'SELECT assignment_id, assignment_name, assignment_description,
			assignment_due, assignment_points
		FROM assignment
		WHERE assignment_id = :assignment';

	$assignmentStmt = $db->prepare($query);
	$assignmentStmt->bindParam(':assignment', $argv[1], PDO::PARAM_INT);
	$assignmentStmt->execute();
	$template['assignment'] = $assignmentStmt->fetch(PDO::FETCH_ASSOC);
	$assignmentStmt = null;

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
