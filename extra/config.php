<?php
session_start();
$sitename = 'spamdforum';
$protocol = 'http';
$server = $_SERVER['HTTP_HOST'];
//$server = 'hostname.com';
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/variables.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config_access.php");

?>
