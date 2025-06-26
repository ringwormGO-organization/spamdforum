<?php
/* See file COPYING for permissions and conditions to use the file. */

function security_validateupdateinfo($dbc, $auth=NULL) {
	global $server, $protocol, $table, $config;
	// this function checks if the $_SESSION['auth'] matches the PASSWORD in the database table.
	if (isset($auth)) {
		if ($_SESSION['session_last_ip'] != inet_pton($_SERVER['REMOTE_ADDR'])) {
			session_commit();
			setcookie(session_name(), '', time()-300, '/', 0);
			session_id(session_create_id());
			goto redirect;
		}
		$uinfo = get_user_info("WHERE password=?", "user_id, email, "
				     . "name, powerlevel", [$auth]);
		if ($uinfo) {
			foreach ($uinfo as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else {
			$_SESSION = array();
			goto logout;
		}
	} else {
		logout:
		global $auth;
		$auth = NULL;
		$_SESSION = array();
		session_destroy();

		redirect:
		global $auth;
		$auth = NULL;
		session_start();
		$_SESSION['powerlevel'] = -1;
		$_SESSION['email'] = '';
		global $anonymous_access, $anonymous_page;
		if ($config['anonymous_access'] == false) {
			if (empty($config['anonymous_page'])) {
				header("Location: $protocol://$server/account/login.php");
				exit;
			}
			$anonymous_page = $config['anonymous_page'];
			header("Location: $protocol://$server$anonymous_page");
			exit;
		}
	}
}
	
function security_authlastvisit($dbc, $auth, $ip) {
	// insert last visit into database
	global $table;
	$now = date("Y-m-d H:i:s");
	$query = "UPDATE $table SET last_visit=?, last_ip=? WHERE "
		."password = '$auth'";
	$result = mysqli_execute_query($dbc, $query, [$now, $ip]);
	if ($result) {
		return true;
	} else {
		return false;
	}
}
?>
