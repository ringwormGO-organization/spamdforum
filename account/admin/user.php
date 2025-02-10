<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");

$msg = NULL;
$ok = FALSE;
if (isset($_POST['delete'])) {
	$mypwlvl = $_SESSION['powerlevel'];
	if ($mypwlvl < 100) {
		$msg .= $words['msg']['err_nopriv'];
		goto stop;
	}
	if (!isset($_POST['delete_email'])) {
		goto stop;
	}
	foreach ($_POST['delete_email'] as $key => $email) {
		$query = "SELECT powerlevel FROM $table WHERE email=?";
		$result = mysqli_execute_query($dbc, $query, [$email]);
		if (mysqli_num_rows($result) < 1) {
			$msg .= $words['msg']['err_not_found'];
			goto stop;
		}
		$assoc = mysqli_fetch_assoc($result);
		if ($assoc['powerlevel'] >= $mypwlvl) {
			$msg .= $words['msg']['err_priv_unmet'];
			goto stop;
		}
		$delete_list[] = $email;
		if (!isset($target_num)) {
			$target_num = 1;
		} else {
			$target_num++;
		}
		$msg .= "{$words['msg']['added_to_list']} $email ({$assoc['powerlevel']}) \n";
	}
	if (isset($target_num)) {
		$placeholder_email = "?" . str_repeat(",?", $target_num - 1);
		$query = "DELETE FROM $table WHERE email IN ($placeholder_email) LIMIT $target_num";
		$result = mysqli_execute_query($dbc, $query, $delete_list);
		$delete_num = $target_num - mysqli_affected_rows($dbc);
		$msg .= "{$words['msg']['delete_request']} $target_num, {$usertablephp['msg']['delete_failed']}: $delete_num";
	}
}

?>
<?php
	stop:
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<h1><?=$words['h1_title']; ?></h1>
<p style="color:red;"><?=$words['p_red_notice']; ?></p>

<?php
	if (isset($msg)) {
		$msg = nl2br($msg, false);
		echo "<p style=\"color: red;\">$msg</p>";
	}

	$query = "SELECT user_id, name, email, reg_date, last_visit FROM $table ORDER BY reg_date DESC";
	$result = mysqli_execute_query($dbc, $query);
	$num = mysqli_num_rows($result);

	if ($num > 0) {
		echo "<h3>{$words['user_num_msg'][0]} $num</h3>";
		echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
		echo "<table style=\"text-align:center; padding: 2px; \">
<tr>
	<th style=\"width: 0.2%; text-align: left;\">&nbsp;</th>
	<th style=\"width: 1.5%; text-align: left;\">{$words['th_usertable']['id']}</th>
	<th style=\"width: 3.4%; text-align: left;\">{$words['th_usertable']['name']}</th>
	<th style=\"width: 4.6%; text-align: left;\">{$words['th_usertable']['email']}</th>
	<th style=\"width: 2.2%; text-align: left;\">{$words['th_usertable']['reg_date']}</th>
	<th style=\"width: 2.5%; text-align: left;\">{$words['th_usertable']['last_visit']}</th>
</tr>
\n";
		while ($row = mysqli_fetch_row($result)) {
			foreach ($row as $key => $value) {
				$row[$key] = export_data($value);
			}
			echo "
<tr>
	<td style=\"width: 0.2%; text-align: left;\">&nbsp;</td>
	<td style=\"width: 1.5%; text-align: left;\"><input type=\"checkbox\" name=\"delete_email[]\" value=\"$row[2]\">$row[0]</td>
	<td style=\"width: 3.4%; text-align: left;\"><a href=\"$protocol://$server/profiles.php?email=$row[2]\">$row[1]</a></td>
	<td style=\"width: 4.6%; text-align: left;\"><a href=\"mailto:$row[2]\">$row[2]</a></td>
	<td style=\"width: 2.2; text-align: left;\">$row[3]</td>
	<td style=\"width: 2.5%; text-align: left;\">$row[4]</td>
</tr>
";
		}
		echo '</table>';
		echo "<div style=\"text-align: center;\"><input type=\"submit\" name=\"delete\" value=\"{$words['input']['delete']}\"></div>";
		echo '</form>';
	} else {
		echo "<p>{$words['user_num_msg'][1]}</p>";
	}
?>


<!-- KET THUC NOI DUNG TRANG -->
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
