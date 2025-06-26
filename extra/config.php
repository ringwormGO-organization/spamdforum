<?php
/* See file COPYING for permissions and conditions to use the file. */
$config = parse_ini_file("{$_SERVER['DOCUMENT_ROOT']}/extra/config.ini");

$protocol = $config['protocol'];
$server = $config['server'];
if ($protocol == 'https' || isset($_SERVER['HTTPS']))
	$config['secure'] = true;
session_set_cookie_params(['lifetime' => time() + 2592000, 'path' => '/', 'domain' => '', 'secure' => $config['secure'], 'httponly' => true]);
session_start();
if(!isset($need_db))
	$need_db = true;
if($need_db)
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/dbconnect.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/variables.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/functions.php");

if (!empty($anonymous_page)) {
	$config['anon_pages'][] = $config['anonymous_page'];
}

if (!in_array($_SERVER['SCRIPT_NAME'], $config['anon_pages'])) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/security.php");
	$auth = NULL;
	if (isset($_SESSION['auth'])) {
		$auth = &$_SESSION['auth'];
	}
	security_validateupdateinfo($dbc, $auth);
	security_authlastvisit($dbc, $auth, inet_pton($_SERVER['REMOTE_ADDR']));
}

require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
?>
