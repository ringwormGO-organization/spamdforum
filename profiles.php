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
	$email = $_GET['email'];
	$uinfo = get_user_info("WHERE email=?", "user_id, name, powerlevel, "
			     . "reg_date, last_visit", [$email]);
	if (!$uinfo) {
		echo "<h3>{$profilesphp['err_not_found']}</h3>";
		goto stop;
	}
	foreach ($uinfo as $key => $value) {
		$uinfo[$key] = export_data($value);
	}
	if ($uinfo['powerlevel'] >= 0) {
		echo "<h2><a href=\"$protocol://$server/profiles.php" .
		    "?email=$email\">{$uinfo['name']}</a> "
		  . "({$uinfo['powerlevel']}) "
		  . "&lt;<a href=\"mailto:$email\">$email</a>&gt;</h2>";
	} else {
		echo "<h2><del><a href=\"$protocol://$server/" .
		    "profiles.php?email=$email\">{$uinfo['powerlevel']}</a>" .
		    "</del> ({$uinfo['name']}) &lt;<a href=\"mailto:$email\">" .
		    "$email</a>&gt;</h2>";
	}
	echo "<h3>{$profilesphp['reg_date']}: {$uinfo['reg_date']}</h3>";
	echo "<p>{$profilesphp['last_visit']}: {$uinfo['last_visit']}</p>";
}
?>
<?php
stop:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
