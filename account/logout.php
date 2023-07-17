<?php
	$title = "Dang xuat";
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<!-- BAT DAU NOI DUNG TRANG -->

<h1>Dang xuat</h1>
<?php
	if (isset($_SESSION['auth'])) {
		$_SESSION = array();
		session_destroy();
		setcookie(session_name(), '', time()-1, '/', 0);
		header("Location: $protocol://$server/account/login.php");
                exit();
	} else {
		header("Location: $protocol://$server/index.php");
		exit();
	}
?>
<!-- KET THUC NOI DUNG TRANG -->

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
