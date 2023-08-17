<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
$title = "Ho so nguoi dung";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<?php
if (!empty($_GET['email'])) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	$email = escape_data($_GET['email']);
	$query = "SELECT user_id, name, powerlevel, reg_date, last_visit FROM forum_user WHERE email=?";
	$result = mysqli_execute_query($dbc, $query, [$email]);
	if (mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_row($result);
		foreach ($userdata as $key => $value) {
			$userdata[$key] = export_data($value);
		}
		echo "<h2><a href=\"$protocol://$server/profiles.php?email=$email\">$userdata[1] ($userdata[2])</h2><p><a href=\"mailto:$email\">$email</a></p>";
		echo "<h3>Ngay dang ky: {$userdata[3]}</h3>";
		echo "<p>Truy cap lan cuoi: {$userdata[4]}</p>";
	} else {
		echo "<h3>Khong tim thay nguoi dung!</h3>";
	}
}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
