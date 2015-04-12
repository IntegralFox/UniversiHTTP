<?php

/* Handle file operations for assignments */

if ($argc == 2 && $argv[1] == 'upload') {
	$assignmentId = $_POST['assignment'];
	$parentFolderId = $_POST['parent'];
	if ($parentFolderId == 0) $parentFolderId = null;
	$file = $_FILES['file'];
	$fp = fopen($file['tmp_name'], 'rb');

	$db = pdoConn();

	$query = 'SELECT count(file_id) AS count, file_id
		FROM file
		WHERE file_name = :name
		AND assignment_id = :assignment
		AND user_id = :user';

	if ($parentFolderId) $query .= ' AND folder_id = :folder'; // Numeric folder id
	else $query .= ' AND folder_id IS :folder'; // Null folder id (= NULL is never true)

	$existsStmt = $db->prepare($query);
	$existsStmt->bindParam(':folder', $parentFolderId, PDO::PARAM_INT);
	$existsStmt->bindParam(':name', $file['name'], PDO::PARAM_STR);
	$existsStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$existsStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$existsStmt->execute();
	$fileExists = $existsStmt->fetch(PDO::FETCH_ASSOC);
	$existsStmt = null;

	if ($fileExists['count']) {
		$query = 'UPDATE file
			SET file_binary = :bin
			WHERE file_id = :file';

		$fileStmt = $db->prepare($query);
		$fileStmt->bindParam(':bin', $fp, PDO::PARAM_LOB);
		$fileStmt->bindParam(':file', $fileExists['file_id'], PDO::PARAM_INT);
		$fileStmt->execute();
		$fileStmt = null;
	} else {
		$query = 'INSERT INTO file (file_name, file_size, file_mime_type,
				file_binary, folder_id, assignment_id, user_id)
			VALUES (:name, :size, :mime, :bin, :folder, :assignment, :user)';

		$fileStmt = $db->prepare($query);
		$fileStmt->bindParam(':name', $file['name'], PDO::PARAM_STR);
		$fileStmt->bindParam(':size', $file['size'], PDO::PARAM_INT);
		$fileStmt->bindParam(':mime', $file['type'], PDO::PARAM_STR);
		$fileStmt->bindParam(':bin', $fp, PDO::PARAM_LOB);
		$fileStmt->bindParam(':folder', $parentFolderId, PDO::PARAM_INT);
		$fileStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$fileStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
		$fileStmt->execute();
		$fileStmt = null;
	}

	$db = null;

	fclose($fp);
} else if ($argc == 2 && $argv[1] == 'rename') {
	$fileId = $_POST['file'];
	$name = $_POST['name'];

	$db = pdoConn();

	$query = 'UPDATE file
		SET file_name = :name
		WHERE file_id = :file
		AND user_id = :user';

	$fileStmt = $db->prepare($query);
	$fileStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$fileStmt->bindParam(':file', $fileId, PDO::PARAM_INT);
	$fileStmt->bindParam(':name', $name, PDO::PARAM_STR);
	$fileStmt->execute();
	$fileStmt = null;

	$db = null;
} else if ($argc == 2 && $argv[1] == 'delete') {
	$fileId = $_POST['file'];

	$db = pdoConn();

	$query = 'DELETE FROM file
		WHERE file_id = :file
		AND user_id = :user';

	$fileStmt = $db->prepare($query);
	$fileStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$fileStmt->bindParam(':file', $fileId, PDO::PARAM_INT);
	$fileStmt->execute();
	$fileStmt = null;

	$db = null;
} else if ($_SESSION['faculty'] && $argc == 6 && $argv[1] == 'json' && $argv[2] == 'assignment' && $argv[4] == 'user') {
	$assignmentId = $argv[3];
	$userId = $argv[5];

	$db = pdoConn();

	$query = 'SELECT file_id, file_name, folder_id
		FROM file
		WHERE user_id = :user
		AND assignment_id = :assignment
		ORDER BY file_name';

	$fileStmt = $db->prepare($query);
	$fileStmt->bindParam(':user', $userId, PDO::PARAM_INT);
	$fileStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$fileStmt->execute();
	$results = $fileStmt->fetchAll(PDO::FETCH_ASSOC);
	$fileStmt = null;

	$db = null;

	header('Content-Type: application/json');
	echo json_encode($results);
} else if ($argc == 4 && $argv[1] == 'json' && $argv[2] == 'assignment') {
	$assignmentId = $argv[3];

	$db = pdoConn();

	$query = 'SELECT file_id, file_name, folder_id
		FROM file
		WHERE user_id = :user
		AND assignment_id = :assignment
		ORDER BY file_name';

	$fileStmt = $db->prepare($query);
	$fileStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$fileStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$fileStmt->execute();
	$results = $fileStmt->fetchAll(PDO::FETCH_ASSOC);
	$fileStmt = null;

	$db = null;

	header('Content-Type: application/json');
	echo json_encode($results);
}
