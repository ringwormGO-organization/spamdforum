<?php
$_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
$_SERVER['PATH_INFO'] = htmlspecialchars($_SERVER['PATH_INFO']);
print_r($_SERVER);
?>
