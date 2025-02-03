<?php
DEFINE ('DB_ID', 'spamdforum');
DEFINE ('DB_PW', 'spamdforum');
DEFINE ('DB_HOST', 'p:127.0.0.1');
DEFINE ('DB_NAME', 'spamdforum');
$table = 'forum_user';
$msgtable = 'forum_msg';
$votetable = 'forum_votes';

$dbc = mysqli_init();

if (!mysqli_real_connect($dbc, DB_HOST, DB_ID, DB_PW, DB_NAME)) {
	die("Khong the thiet lap ket noi den co so du lieu!");
}

mysqli_set_charset($dbc, "utf8mb4");

function 
email_exists($email, $dbc) 
{
	global $table;
	$query = "SELECT user_id FROM $table WHERE email=?";
	$result = @mysqli_execute_query($dbc, $query, [$email]);

	if (mysqli_num_rows($result) == 0) {
		return FALSE;
		mysqli_free_result($dbc, $result);
	} else {
		return TRUE;
		mysqli_free_result($dbc, $result);
	}
}

function export_data($data) {
	return htmlspecialchars($data);
}
?>
