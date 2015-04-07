<?php
/* Site-wide utility functions */

require_once('../constant/secureConstants.php');

function mysqliConn() {
	$db = new mysqli(
		$GLOBALS['DB']['SERVER'],
		$GLOBALS['DB']['USERNAME'],
		$GLOBALS['DB']['PASSWORD'],
		$GLOBALS['DB']['DATABASE']
	);

	if ($db->connect_errno) {
		header('HTTP/1.1 500 Internal Server Error');
		exit('Database connection failed. Contact system administrator. Error code: ' . $db->connect_errno);
	}

	return $db;
}

function pdoConn() {
	$dsn = "mysql:dbname={$GLOBALS['DB']['DATABASE']};host={$GLOBALS['DB']['SERVER']}";

	try {
		$db = new PDO($dsn, $GLOBALS['DB']['USERNAME'],	$GLOBALS['DB']['PASSWORD'], [PDO::ATTR_PERSISTENT => true]);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		header('HTTP/1.1 500 Internal Server Error');
		exit('Database connection failed. Contact system administrator. Error code: ' . $e->getMessage());
	}

	return $db;
}

function utcToLocal($d) {
	$localTimeZone = new DateTimeZone('America/Chicago');
	$utcTimeZone = new DateTimeZone('UTC');

	$datetime = new DateTime($d, $utcTimeZone);
	$datetime->setTimezone($localTimeZone);
	return $datetime->format("Y/m/d H:i:s");
}

function localToUTC($d) {
	$localTimeZone = new DateTimeZone('America/Chicago');
	$utcTimeZone = new DateTimeZone('UTC');

	$datetime = new DateTime($d, $localTimeZone);
	$datetime->setTimezone($utcTimeZone);
	return $datetime->format("Y/m/d H:i:s");
}
