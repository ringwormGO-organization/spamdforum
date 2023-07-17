<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
	$msg = NULL;
	if (isset($_POST['delete'])) {
		$mypowerlevel = $_COOKIE['powerlevel'];
		if ($mypowerlevel >= 100) {
			if (isset($_POST['delete_email'])) {
				foreach ($_POST['delete_email'] as $key => $email) {
					$email = escape_data($email);
					$query = "SELECT powerlevel FROM forum_user WHERE email='$email'";
					$result = @mysqli_query($dbc, $query);
					if (mysqli_num_rows($result) == 1) {
						$assoc = mysqli_fetch_assoc($result);
						if ($assoc['powerlevel'] < $mypowerlevel) {
							$query = "DELETE FROM forum_user WHERE email='$email' LIMIT 1";
							$result = @mysqli_query($dbc, $query);
							if (mysqli_affected_rows($dbc) == 1) {
								$msg .= "{$usertablephp['msg']['delete_success']} $email ({$assoc['powerlevel']}) \n";
							} else {
								$msg .= "{$usertablephp['msg']['delete_failed']} $email ({$assoc['powerlevel']}) ! \n";
							}
						} else {
							$msg .= $usertablephp['msg']['err_priv_unmet'];
						}
					} else {
						$msg .= $usertablephp['msg']['err_not_found'];
					}
				}
			}
		} else {
			$msg .= $usertablephp['msg']['err_nopriv'];
		}
	}

?>


<?php
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<h1><?=$usertablephp['h1_title']; ?></h1>    
<p style="color:red;"><?=$usertablephp['p_red_notice']; ?></p>

<?php
	if (isset($msg)) {
		$msg = nl2br($msg);
		echo "<p style=\"color: red;\">$msg</p>";
	}

	$query = "SELECT user_id, name, email, reg_date, last_visit FROM forum_user ORDER BY reg_date DESC";
	$result = @mysqli_query($dbc, $query);
	$num = mysqli_num_rows($result);

	if ($num > 0) {
		echo "<h3>{$usertablephp['user_num_msg'][0]} $num</h3>";
		echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
		echo "<table style=\"text-align:center; padding: 2px; \">
			<tr>
				<th style=\"width: 0.2%; text-align: left;\">&nbsp;</th>
				<th style=\"width: 1.5%; text-align: left;\">{$usertablephp['th_usertable']['id']}</th>
				<th style=\"width: 3.4%; text-align: left;\">{$usertablephp['th_usertable']['name']}</th>
				<th style=\"width: 4.6%; text-align: left;\">{$usertablephp['th_usertable']['email']}</th>
				<th style=\"width: 2.2%; text-align: left;\">{$usertablephp['th_usertable']['reg_date']}</th>
				<th style=\"width: 2.5%; text-align: left;\">{$usertablephp['th_usertable']['last_visit']}</th>
			</tr>
			\n";
		while ($row = mysqli_fetch_row($result)) {
			echo "
			<tr>
				<td style=\"width: 0.2%; text-align: left;\">&nbsp;</td>
				<td style=\"width: 1.5%; text-align: left;\"><input type=\"checkbox\" name=\"delete_email[]\" value=\"$row[2]\">$row[0]</td>
                                <td style=\"width: 3.4%; text-align: left;\"><a href=\"http://$server/profiles.php?email=$row[2]\">$row[1]</a></td>
                                <td style=\"width: 4.6%; text-align: left;\"><a href=\"mailto:$row[2]\">$row[2]</a></td>
                                <td style=\"width: 2.2; text-align: left;\">$row[3]</td>
                                <td style=\"width: 2.5%; text-align: left;\">$row[4]</td>
			</tr>
			";
		}
		echo '</table>';
		echo "<div style=\"text-align: center;\"><input type=\"submit\" name=\"delete\" value=\"{$usertablephp['input']['delete']}\"></div>";
		echo '</form>';
		mysqli_free_result($result);
	} else {
		echo "<p>{$usertablephp['user_num_msg'][1]}</p>";
	}
?>


<!-- KET THUC NOI DUNG TRANG -->
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
