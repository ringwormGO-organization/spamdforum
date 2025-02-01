<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php

function security_validateupdateinfo($dbc, $auth=NULL) {
	global $server, $protocol, $table;
	// this function checks if the $_SESSION['auth'] matches the PASSWORD in the database table.
	if (isset($auth)) {
		$uinfo = get_user_info("WHERE password=?", "user_id, email, "
				     . "name, powerlevel", [$auth]);
		if ($uinfo) {
			foreach ($uinfo as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else {
			goto logout;
		}
	} else {
		logout:
		$_SESSION = array();
		$_SESSION['powerlevel'] = 0;
		global $anonymous_access, $anonymous_page;
		if ($anonymous_access == false) {
			if (empty($anonymous_page)) {
				header("Location: $protocol://$server/account/login.php");
				exit;
			}
			header("Location: $protocol://$server$anonymous_page");
			exit;
		}
	}
}
	
function security_authlastvisit($dbc, $auth, $ip) {
	// insert last visit into database
	global $table;
	$now = date("Y-m-d H:i:s");
	$query = "UPDATE $table SET last_visit='$now', last_ip='$ip' WHERE password = '$auth'";
	$result = mysqli_query($dbc, $query);
	if ($result) {
		return true;
	} else {
		return false;
	}
}
?>
