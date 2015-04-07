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

	if ($parentFolderId) $query .= ' AND folder_id = :file'; // Numeric folder id
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
} else if ($argc == 3 && $argv[1] == 'json') {
	$assignmentId = $argv[2];
	$query = 'SELECT file_id';
}
