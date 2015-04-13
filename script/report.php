<?php

/* Generate reports */

if ($argc == 2 && $argv[1] == 'grades') {
	$db = pdoConn();

	if ($_SESSION['faculty']) {
		$query = 'SELECT course_id, course_number, course_title,
				MIN(grade_points / assignment_points * 100) AS grade_min,
				AVG(grade_points / assignment_points * 100) AS grade_mean,
				MAX(grade_points / assignment_points * 100) AS grade_max
			FROM course
			INNER JOIN assignment USING (course_id)
			INNER JOIN grade USING (assignment_id)
			WHERE course.user_id = :user -- this faculty members courses
			GROUP BY course_id
			ORDER BY course_number ASC';
	} else {
		$query = 'SELECT course_id, course_number, course_title,
				MIN(grade_points / assignment_points * 100) AS grade_min,
				AVG(grade_points / assignment_points * 100) AS grade_mean,
				MAX(grade_points / assignment_points * 100) AS grade_max
			FROM course
			INNER JOIN assignment USING (course_id)
			INNER JOIN grade USING (assignment_id)
			WHERE grade.user_id = :user -- this students grades
			GROUP BY course_id
			ORDER BY course_number ASC';
	}

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['course'] = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$db = null;

	require('../template/reportGradesAll.php');
} else if ($_SESSION['faculty'] && $argc == 4 && $argv[1] == 'grades' && $argv[2] == 'course') {
	$courseId = $argv[3];
	$userId = $_SESSION['userId'];

	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title
		FROM course
		WHERE course_id = :course';

	$courseStmt = $db->prepare($query);
	$courseStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$courseStmt->execute();
	$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$query = 'SELECT assignment_id, assignment_name,
			MIN(grade_points / assignment_points * 100) AS grade_min,
			AVG(grade_points / assignment_points * 100) AS grade_mean,
			MAX(grade_points / assignment_points * 100) AS grade_max
		FROM course
		INNER JOIN assignment USING (course_id)
		INNER JOIN grade USING (assignment_id)
		WHERE course.user_id = :user -- ensure this user is the data owner
		AND course_id = :course -- assignments in this course
		GROUP BY assignment_id
		ORDER BY assignment_due ASC';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['assignment'] = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$query = 'SELECT user.user_id, user_name_last, user_name_first, user_name_middle,
			MIN(grade_points / assignment_points * 100) AS grade_min,
			AVG(grade_points / assignment_points * 100) AS grade_mean,
			MAX(grade_points / assignment_points * 100) AS grade_max
		FROM user
		INNER JOIN course_user_bridge USING (user_id)
		INNER JOIN course USING (course_id)
		INNER JOIN assignment USING (course_id)
		LEFT JOIN grade ON user.user_id = grade.user_id AND assignment.assignment_id = grade.assignment_id
		WHERE course.user_id = :user -- returns nothing if user is not data owner
		AND course_id = :course -- users in this course
		GROUP BY user.user_id
		ORDER BY user_name_last ASC, user_name_first ASC, user_name_middle ASC';

	$userStmt = $db->prepare($query);
	$userStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$userStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$userStmt->execute();
	$template['student'] = $userStmt->fetchAll(PDO::FETCH_ASSOC);
	$userStmt = null;

	$db = null;

	require('../template/reportGradesCourse.php');
} else if (!$_SESSION['faculty'] && $argc == 4 && $argv[1] == 'grades' && $argv[2] == 'course') {
	$courseId = $argv[3];
	$userId = $_SESSION['userId'];

	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title
		FROM course
		WHERE course_id = :course';

	$courseStmt = $db->prepare($query);
	$courseStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$courseStmt->execute();
	$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
		FROM user
		WHERE user_id = :user';

	$userStmt = $db->prepare($query);
	$userStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$userStmt->execute();
	$template['student'] = $userStmt->fetch(PDO::FETCH_ASSOC);
	$userStmt = null;

	$query = 'SELECT assignment_id, assignment_name, assignment_points, grade_points, (grade_points / assignment_points * 100) AS grade_percent
		FROM assignment
		INNER JOIN grade USING (assignment_id)
		WHERE course_id = :course
		AND user_id = :user
		ORDER BY assignment_due ASC';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['assignment'] = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$query = 'SELECT SUM(grade_points) AS grade_points, SUM(assignment_points) AS assignment_points, (SUM(grade_points) / SUM(assignment_points) * 100) AS grade_percent
		FROM assignment
		INNER JOIN grade USING (assignment_id)
		WHERE course_id = :course
		AND user_id = :user';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['calculation'] = $gradeStmt->fetch(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$db = null;

	require('../template/reportGradesStudent.php');
} else if ($_SESSION['faculty'] && $argc == 6 && $argv[1] == 'grades' && $argv[2] == 'course' && $argv[4] = 'user') {
	$courseId = $argv[3];
	$userId = $argv[5];

	$db = pdoConn();

	$query = 'SELECT course_id, course_number, course_title
		FROM course
		WHERE course_id = :course';

	$courseStmt = $db->prepare($query);
	$courseStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$courseStmt->execute();
	$template['course'] = $courseStmt->fetch(PDO::FETCH_ASSOC);
	$courseStmt = null;

	$query = 'SELECT user_id, user_name_last, user_name_first, user_name_middle
		FROM user
		WHERE user_id = :user';

	$userStmt = $db->prepare($query);
	$userStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$userStmt->execute();
	$template['student'] = $userStmt->fetch(PDO::FETCH_ASSOC);
	$userStmt = null;

	$query = 'SELECT assignment_id, assignment_name, assignment_points, grade_points, (grade_points / assignment_points * 100) AS grade_percent
		FROM assignment
		INNER JOIN grade USING (assignment_id)
		WHERE course_id = :course
		AND user_id = :user
		ORDER BY assignment_due ASC';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['assignment'] = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$query = 'SELECT SUM(grade_points) AS grade_points, SUM(assignment_points) AS assignment_points, (SUM(grade_points) / SUM(assignment_points) * 100) AS grade_percent
		FROM assignment
		INNER JOIN grade USING (assignment_id)
		WHERE course_id = :course
		AND user_id = :user';

	$gradeStmt = $db->prepare($query);
	$gradeStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$gradeStmt->bindParam(':course', $courseId, PDO::PARAM_INT);
	$gradeStmt->execute();
	$template['calculation'] = $gradeStmt->fetch(PDO::FETCH_ASSOC);
	$gradeStmt = null;

	$db = null;

	require('../template/reportGradesStudent.php');
}

?>
