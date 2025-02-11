<?php
function newmsg($dbc, $msg_arr) {
	global $msgtable;
	$now = date("Y-m-d H:i:s");
	$q = "INSERT INTO $msgtable (relate_to, subject, body, from_addr, "
	   . "to_addr, r_pwlvl, w_pwlvl, last_edit, created_at) VALUES "
	   . "(?, ?, ?, ?, ?, ?, ?, '$now', '$now')";
	if (mysqli_execute_query($dbc, $q, $msg_arr))
		return mysqli_insert_id($dbc);
	else
		return FALSE;
}
function editmsg($dbc, $msg_id, $changed_col, $msg_arr) {
	global $msgtable;
	$now = $msgtable;
	$q = "UPDATE $msgtable SET $changed_col WHERE msg_id=?";
	$msg_arr[] = $msg_id;
	if (mysqli_execute_query($dbc, $q, $msg_arr)) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function replacebadchars($data) {
	$data = str_replace("\r", "", $data);
	$data = str_replace("“", "\"", $data);
	$data = str_replace("”", "\"", $data);
	$data = str_replace("…", "...", $data);
	$data = str_replace("–", "-", $data);
	$data = str_replace("‘", "'", $data);
	$data = str_replace("’", "'", $data);
	return $data;
}
?>
