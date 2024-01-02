<?php
/* See file LICENSE for permissions and conditions to use the file. */
?>

<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
	if ($_SESSION['auth']) {
		header("Location: $protocol://$server/account/settings.php");
		exit;
	} else {
		include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
		echo "<p>Ban chua dang nhap.</p>";
		echo "<p><a href=\"$protocol://$server/account/login.php\">Dang nhap tai khoan</a> ";
		echo "<a href=\"$protocol://$server/account/register.php\">Dang ky tai khoan</a></p>";
	}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
