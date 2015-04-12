<?php

/* Handle serving files in the database for assignments */

if ($_SESSION['faculty']) {
	// Too Few Arguments / Improper arguments
	if ($argc < 5 || $argv[1] != 'assignment' || $argv[3] != 'user') h400();
	// A request for the root of the assignment without the trailing
	// slash will break any relative urls
	if ($argc == 5 && substr($_SERVER['REQUEST_URI'], -1) != '/') hAddSlash();
	$assignmentId = $argv[2];
	$studentId = $argv[4];
	$index = 5;
} else {
	if ($argc < 3 || $argv[1] != 'assignment') h400();
	if ($argc == 3 && substr($_SERVER['REQUEST_URI'], -1) != '/') hAddSlash();
	$assignmentId = $argv[2];
	$studentId = $_SESSION['userId'];
	$index = 3;
}

$db = pdoConn();

$folderQuery = 'SELECT folder_id, folder_name, folder_parent_id
	FROM folder
	WHERE assignment_id = :assignment
	AND user_id = :user';
$fileQuery = 'SELECT file_id, file_name, folder_id
	FROM file
	WHERE assignment_id = :assignment
	AND user_id = :user';
$fetchQuery = 'SELECT file_binary, file_mime_type, file_size
	FROM file
	WHERE file_id = :file';

$folderStmt = $db->prepare($folderQuery);
$folderStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
$folderStmt->bindParam(':user', $studentId, PDO::PARAM_INT);
$folderStmt->execute();
$folder = $folderStmt->fetchAll(PDO::FETCH_ASSOC);
$folderStmt = null;

$fileStmt = $db->prepare($fileQuery);
$fileStmt->bindParam(':assignment', $assignmentId, PDO::PARAM_INT);
$fileStmt->bindParam(':user', $studentId, PDO::PARAM_INT);
$fileStmt->execute();
$file = $fileStmt->fetchAll(PDO::FETCH_ASSOC);
$fileStmt = null;

$folderId = null;
$fileId = null;
$found = false;

if (($_SESSION['faculty'] && $argc == 5) || $argc == 3) {
	$fileName = 'index.html';
} else {
	while ($index < $argc - 1) {
		$found = false;
		foreach ($folder as $f) {
			if ($f['folder_parent_id'] == $folderId && $f['folder_name'] == $argv[$index]) {
				$found = true;
				$folderId = $f['folder_id'];
			}
		}
		if (!$found) h404();
		++$index;
	}

	$fileName = $argv[$index];

	foreach ($folder as $f) {
		if ($f['folder_parent_id'] == $folderId && $f['folder_name'] == $argv[$index]) {
			$folderId = $f['folder_id'];
			$fileName = 'index.html';
			if (substr($_SERVER['REQUEST_URI'], -1) != '/') {
				// The request is for a folder but does not end in a /
				// Any relative urls will fail so redirect with a trailing /
				hAddSlash();
			}
		}
	}
}

$found = false;
foreach ($file as $f) {
	if ($f['folder_id'] == $folderId && $f['file_name'] == $fileName) {
		$found = true;
		$fileId = $f['file_id'];
	}
}

if (!$found) h404();

$fetchStmt = $db->prepare($fetchQuery);
$fetchStmt->bindParam(':file', $fileId, PDO::PARAM_INT);
$fetchStmt->execute();
$fetchStmt->bindColumn('file_binary', $fileBinary, PDO::PARAM_LOB);
$fetchStmt->bindColumn('file_mime_type', $fileMime, PDO::PARAM_STR);
$fetchStmt->bindColumn('file_size', $fileSize, PDO::PARAM_INT);
$fetchStmt->fetch(PDO::FETCH_BOUND);
$fetchStmt = null;

$db = null;

header('Content-Type: ' . $fileMime);
header('Content-Size: ' . $fileSize);
echo $fileBinary;

function h404() {
	http_response_code(404);
	exit('UniversiHTTP - Requested Resource Does Not Exist In Database');
}

function h400() {
	http_response_code(400);
	exit('UniversiHTTP - Malformed Request To Serve File');
}

function hAddSlash() {
	header('Location: '. $_SERVER['REQUEST_URI'] . '/');
	exit();
}

?>
