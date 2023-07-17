<?php

function security_validateupdateinfo($dbc, $auth) {
	global $server, $protocol;
	// this function checks if the $_SESSION['auth'] matches the PASSWORD in the database table.
	$query = "SELECT user_id, email, name, powerlevel FROM forum_user WHERE password='$auth'";
	$result = mysqli_query($dbc, $query);
	if (mysqli_num_rows($result) == 1) {
		$user = mysqli_fetch_assoc($result);
		$secure = false;
		if ($protocol == "https" && !empty($_SERVER['HTTPS'])) {
			// prefer setting cookies over secure connections
			$secure = true;
		}
		foreach ($user as $key => $value) {
			// these cookies are for easier code writting :)
			// they are really secure, the server will ignore even if the user input malformed cookies
			setcookie($key, $value, 0, '/', '', $secure, true);
		}
	} else {
		$_SESSION = array();
		header("Location: $protocol://$server/account/login.php");
                exit;
	}
}
	
function security_authlastvisit($dbc, $auth, $ip) {
	// insert last visit into database
	$query = "UPDATE forum_user SET last_visit=NOW(), last_ip='$ip' WHERE password = '$auth'";
	$result = mysqli_query($dbc, $query);
	if ($result) {
		return true;
	} else {
		return false;
	}
}
?>
