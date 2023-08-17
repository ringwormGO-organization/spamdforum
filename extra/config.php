<?php
$sitename = 'spamdforum';
$protocol = 'http';
$server = $_SERVER['HTTP_HOST'];
//$server = 'hostname.com';
$secure = false;
if ($protocol == 'https' && $_SERVER['HTTPS']) {
	$secure = true;
}
session_set_cookie_params(['lifetime' => time() + 2592000, 'path' => '/', 'domain' => '', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax']);
session_start();
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/variables.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config_access.php");

?>
