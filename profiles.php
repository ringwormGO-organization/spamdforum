<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
$title = "Ho so nguoi dung";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<?php
if (!empty($_SERVER['PATH_INFO']) || isset($_GET['email'])) {
	//$email = explode("/", $_SERVER['PATH_INFO']); // not working because /musicscratchgame2009@gmail.com
	// will implement both /emailaddr and ?email=emailaddr, but prefer the first one
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	$email = escape_data($_GET['email']);
	//$query = "SELECT user_id, name, powerlevel, reg_date, last_visit FROM forum_user WHERE email='$email[0]'";
	$query = "SELECT user_id, name, powerlevel, reg_date, last_visit FROM forum_user WHERE email='$email'";
	echo "<pre>Cau truy van se duoc thuc hien: $query</pre>";
	$result = mysqli_query($dbc, $query);
	if (mysqli_num_rows($result) == 1) {
		$userdatarow = mysqli_fetch_row($result);
		echo "<h2><a href=\"$protocol://$server/profiles.php?email=$email\">$userdatarow[1] ($userdatarow[2])</h2><p><a href=\"mailto:$email\">$email</a></p>";
		echo "<h3>Ngay dang ky: {$userdatarow[3]}</h3>";
		echo "<p>Truy cap lan cuoi: {$userdatarow[4]}</p>";
	} else {
		echo "<h3>Khong tim thay nguoi dung!</h3>";
	}
}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
