<?php
session_start();
$sitename = 'spamdforum';
$protocol = 'http';
//$server = 'npg2mvrzxadbsv5x6gc3a7ahhrveluyurdpirwfc7mhhpfrvbnvbrdyd.onion';
//$server = '0.0.0.0:8000';
$server = $_SERVER['HTTP_HOST'];


require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/variables.php");

$skip_scripts = array(
	'/account/index.php',
	'/account/login.php',
	'/account/register.php'
);
if (!in_array($_SERVER['SCRIPT_NAME'], $skip_scripts)) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/security.php");
	$auth = $_SESSION['auth'];
	security_validateupdateinfo($dbc, $auth);
	security_authlastvisit($dbc, $auth, $_SERVER['REMOTE_ADDR']);
}

?>
