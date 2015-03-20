<?php

/* Initializes the template variable to put data into */
$template = [
	'user_id' => $_SESSION['userId'],
	'user_name_first' => $_SESSION['firstName'],
	'user_name_last' => $_SESSION['lastName'],
	'faculty' => $_SESSION['faculty']
];
