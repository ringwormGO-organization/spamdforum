<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
function get_user_info($cond, $fields, $arr) {
	global $dbc, $table;
	$query = "SELECT " . $fields . " FROM $table " . $cond;
	$uinfo = mysqli_fetch_assoc(mysqli_execute_query($dbc, $query, $arr));
	return $uinfo;
}
function get_msg_info($cond, $fields, $arr) {
	global $dbc, $msgtable, $table;
	$query = "SELECT " . $fields . " FROM $msgtable " . $cond;
	$msginfo = mysqli_fetch_assoc(mysqli_execute_query($dbc, $query, $arr));
	return $msginfo;
}
function secure_hash($password, $algo=PASSWORD_BCRYPT) {
	switch ($algo) {
	case PASSWORD_ARGON2ID:
		$options = ['memory_cost' => 262144, 'time_cost' => 1, 'threads' => 1];
	case PASSWORD_ARGON2I:
		$options = ['memory_cost' => 131072, 'time_cost' => 5, 'threads' => 1];
	default:
		$options = ['cost' => 13];
	}

	$pwhash = password_hash($password, $algo, $options);
	return $pwhash;
}

function init_user_session($auth) {
	$_SESSION['auth'] = $auth;
	$_SESSION['session_last_ip'] = inet_pton($_SERVER['REMOTE_ADDR']);
}
?>
