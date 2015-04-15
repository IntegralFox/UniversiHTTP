<?php

/* Include Function Library */
require_once('functions.php');


/* Session Authentication
 *
 * Session data is protected by a mutex lock while writable. Free the lock
 * by committing as soon as possible so that concurrent requests complete
 * quickly. */
session_start();

if (empty($_SESSION['userId'])) require('login.php');
if (isset($_SESSION['needsNewPassword']) && $_SESSION['needsNewPassword']) require('password.php');

session_commit();


/* Set Up Template Var */
require('template.php');


/* Query Resolution
 *
 * Split the query string on the slash character to create an array of
 * arguments. Then conditionally include script files to handle requests
 * based on those arguments. */
$request = trim($_SERVER['REQUEST_URI'], '/');
$argv  = explode('/', $request); // $arg will always contain at least one element
$argc  = count($argv);

if ($argv[0] == 'account') {
	require('account.php');
} else if ($argv[0] == 'assignment') {
	require('assignment.php');
} else if ($argv[0] == 'course') {
	require('course.php');
} else if ($argv[0] == 'file') {
	require('file.php');
} else if ($argv[0] == 'folder') {
	require('folder.php');
} else if ($argv[0] == 'logout') {
	require('logout.php');
} else if ($argv[0] == 'password') {
	require('password.php');
} else if ($argv[0] == 'report') {
	require('report.php');
} else if ($argv[0] == 'serve') {
	require('serve.php');
} else {
	require('course.php');
}

?>
