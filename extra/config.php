<?php
session_start();
$sitename = 'spamdforum';
$protocol = 'http';
//$server = 'npg2mvrzxadbsv5x6gc3a7ahhrveluyurdpirwfc7mhhpfrvbnvbrdyd.onion';
//$server = '0.0.0.0:8000';
$server = $_SERVER['HTTP_HOST'];


require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/variables.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config_access.php");

?>
