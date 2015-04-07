<?php

/* Handle folder operations for assignments */

if ($argc == 2 && $argv[1] == 'create') {
	$folderName = $_POST['name'];
	$assignmentId = $_POST['assignment'];
	$parentFolderId = $_POST['parent'];
	if ($parentFolderId == 0) $parentFolderId = null;

	$db = pdoConn();

	$query = 'SELECT count(folder_id)
		FROM folder
		WHERE folder_name = :name
		AND assignment_id = :assignment
		AND user_id = :user';

	if ($parentFolderId) $query .= ' AND folder_parent_id = :folder'; // Numeric folder id
	else $query .= ' AND folder_parent_id IS :folder'; // Null folder id (= NULL is never true)

	$existsStmt = $db->prepare($query);
	$existsStmt->bindParam(':name', $folderName, PDO::PARAM_STR);
	$existsStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$existsStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$existsStmt->bindParam(':folder', $parentFolderId, PDO::PARAM_INT);
	$existsStmt->execute();
	$folderExists = $existsStmt->fetchColumn();
	$existsStmt = null;

	if (!$folderExists) {
		$query = 'INSERT INTO folder (folder_name, folder_parent_id, assignment_id, user_id)
			VALUES (:name, :folder, :assignment, :user)';

		$folderStmt = $db->prepare($query);
		$folderStmt->bindParam(':name', $folderName, PDO::PARAM_STR);
		$folderStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
		$folderStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
		$folderStmt->bindParam(':folder', $parentFolderId, PDO::PARAM_INT);
		$folderStmt->execute();
		$folderStmt = null;
	}

	$db = null;
} else if ($argc == 3 && $argv[1] == 'json') {
	$assignmentId = $argv[2];
	$query = 'SELECT file_id';
}
