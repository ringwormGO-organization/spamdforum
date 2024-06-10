<?php
/* See file COPYING for permissions and conditions to use the file. */
?>

<?php

// --------- User config ----------------------------------------------

$anonymous_access = true;
$anonymous_page = '';
$registration_status = true;

// --------- Scripts --------------------------------------------------

$skip_scripts = array(
	'/account/index.php',
	'/account/login.php',
	'/account/register.php'
);

if (!empty($anonymous_page)) {
	$skip_scripts[] = $anonymous_page;
}

if (!in_array($_SERVER['SCRIPT_NAME'], $skip_scripts)) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/security.php");
	$auth = NULL;
	if (isset($_SESSION['auth'])) {
		$auth = &$_SESSION['auth'];
	}
	security_validateupdateinfo($dbc, $auth);
	security_authlastvisit($dbc, $auth, $_SERVER['REMOTE_ADDR']);
}
?>
