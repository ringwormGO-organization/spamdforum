<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
$title = "Tai khoan";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<?php
	if (isset($_SESSION['auth'])) {
		require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
		$auth = $_SESSION['auth'];
		$query = "SELECT name FROM forum_user WHERE password='$auth'";
		$result = @mysqli_query($dbc, $query);
		if ($result) {
			$assoc = mysqli_fetch_assoc($result);
			echo "<p>Chao mung, {$assoc['name']}!</p>";
			echo "<p><a href=\"$protocol://$server/account/logout.php\">Dang xuat?</a></p>";
		}
	} else {
		echo "<p>Ban chua dang nhap.</p>";
		echo "<p><a href=\"$protocol://$server/account/login.php\">Dang nhap tai khoan</a> ";
		echo "<a href=\"$protocol://$server/account/register.php\">Dang ky tai khoan</a></p>";
	}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
