<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
$title = "Tai khoan";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<?php
	if ($_SESSION['ready'] === TRUE) {
		echo "<p>Chao mung, {$_SESSION['name']}!</p>";
		echo "<p><a href=\"$protocol://$server/account/logout.php\">Dang xuat?</a></p>";
	} else {
		echo "<p>Ban chua dang nhap.</p>";
		echo "<p><a href=\"$protocol://$server/account/login.php\">Dang nhap tai khoan</a> ";
		echo "<a href=\"$protocol://$server/account/register.php\">Dang ky tai khoan</a></p>";
	}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
