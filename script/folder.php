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
} else if ($argc == 2 && $argv[1] == 'rename') {
	$folderId = $_POST['folder'];
	$name = $_POST['name'];

	$db = pdoConn();

	$query = 'UPDATE folder
		SET folder_name = :name
		WHERE folder_id = :folder
		AND user_id = :user';

	$folderStmt = $db->prepare($query);
	$folderStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$folderStmt->bindParam(':folder', $folderId, PDO::PARAM_INT);
	$folderStmt->bindParam(':name', $name, PDO::PARAM_STR);
	$folderStmt->execute();
	$folderStmt = null;

	$db = null;
} else if ($argc == 2 && $argv[1] == 'delete') {
	$folderId = $_POST['folder'];

	$db = pdoConn();

	$query = 'DELETE FROM folder
		WHERE folder_id = :folder
		AND user_id = :user';

	$folderStmt = $db->prepare($query);
	$folderStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$folderStmt->bindParam(':folder', $folderId, PDO::PARAM_INT);
	$folderStmt->execute();
	$folderStmt = null;

	$db = null;
} else if ($argc == 3 && $argv[1] == 'json') {
	$assignmentId = $argv[2];

	$db = pdoConn();

	$query = 'SELECT folder_id, folder_name, folder_parent_id
		FROM folder
		WHERE user_id = :user
		AND assignment_id = :assignment
		ORDER BY folder_name';

	$folderStmt = $db->prepare($query);
	$folderStmt->bindParam(':user', $_SESSION['userId'], PDO::PARAM_INT);
	$folderStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
	$folderStmt->execute();
	$results = $folderStmt->fetchAll(PDO::FETCH_ASSOC);
	$folderStmt = null;

	$db = null;

	header('Content-Type: application/json');
	echo json_encode($results);
}
