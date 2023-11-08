<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<?php
if (!empty($_GET['email'])) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	$email = escape_data($_GET['email']);
	$query = "SELECT user_id, name, password, powerlevel, 
		reg_date, last_visit FROM forum_user WHERE email=?";
	$result = mysqli_execute_query($dbc, $query, [$email]);
	if (mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_row($result);
		foreach ($userdata as $key => $value) {
			$userdata[$key] = export_data($value);
		}
		if (!empty($userdata[2])) {
			echo "<h2><a href=\"$protocol://$server/profiles.php?email=$email\">$userdata[1] ($userdata[3])</a> <a href=\"mailto:$email\">&lt;$email&gt</a></h2>";
		} else {
			echo "<h2 id=\"banneduser\"><a href=\"$protocol://$server/profiles.php?email=$email\">$userdata[1] ($userdata[3])</a> <a href=\"mailto:$email\">&lt;$email&gt</a></h2>";
		}
		echo "<h3>{$profilesphp['reg_date']}: {$userdata[3]}</h3>";
		echo "<p>{$profilesphp['last_visit']}: {$userdata[4]}</p>";
	} else {
		echo "<h3>{$profilesphp['err_not_found']}</h3>";
	}
}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
