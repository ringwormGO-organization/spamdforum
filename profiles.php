<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
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
	$query = "SELECT user_id, name, powerlevel,
		reg_date, last_visit FROM forum_user WHERE email=?";
	$result = mysqli_execute_query($dbc, $query, [$email]);
	if (mysqli_num_rows($result) < 1) {
		echo "<h3>{$profilesphp['err_not_found']}</h3>";
		goto stop;
	}
	$uinfo = mysqli_fetch_row($result);
	foreach ($uinfo as $key => $value) {
		$uinfo[$key] = export_data($value);
	}
	if ($uinfo[2] >= 0) {
		echo "<h2><a href=\"$protocol://$server/profiles.php" .
		    "?email=$email\">$uinfo[1]</a> ($uinfo[2]) " .
		    "<a href=\"mailto:$email\">&lt;$email&gt;</a></h2>";
	} else {
		echo "<h2><del><a href=\"$protocol://$server/" .
		    "profiles.php?email=$email\">$uinfo[1]</a>" .
		    "</del> ($uinfo[2]) <a href=\"mailto:$email\">" .
		    "&lt;$email&gt;</a></h2>";
	}
	echo "<h3>{$profilesphp['reg_date']}: {$uinfo[3]}</h3>";
	echo "<p>{$profilesphp['last_visit']}: {$uinfo[4]}</p>";
}
?>
<?php
stop:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
