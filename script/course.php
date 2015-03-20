<?php

/* Handle course-listing homepage, course pages, and course creation */
if ($argc == 1) {
	// No argument to look up a course. Display the homepage.
	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title
		FROM course ';
	if ($_SESSION['faculty']) {
		$query .= 'WHERE user_id = :userId';
	} else {
		$query .= 'INNER JOIN course_user_bridge USING (course_id)
			WHERE user_id = :userId';
	}

	$courseStmt = $db->prepare($query);
	$courseStmt->execute($_SESSION['userId']);
	$template['course'] = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$db = null;

	require('../template/courseList.php');
}

?>
