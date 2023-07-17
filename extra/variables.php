<?php
$_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
if (isset($_SERVER['PATH_INFO'])) {
	$_SERVER['PATH_INFO'] = htmlspecialchars($_SERVER['PATH_INFO']);
}
?>
