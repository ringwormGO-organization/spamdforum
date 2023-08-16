<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
	$need_msg = FALSE;
	$msg = NULL;

	$mypowerlevel = $_SESSION['powerlevel']; 

	if (isset($_POST['update_power']) && isset($_POST['powerlevel'])) {
		$need_msg = TRUE;
		require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
		foreach($_POST['powerlevel'] as $email => $powerlevel) {
			$email = escape_data($email);
			$powerlevel = intval(escape_data($powerlevel));
			if ($powerlevel <= $mypowerlevel) {
				$query = "SELECT powerlevel FROM $table WHERE email='$email'";
				$result = mysqli_query($dbc, $query);
				if (mysqli_num_rows($result) == 1) {
					$row = mysqli_fetch_row($result);
					if ($row[0] < $mypowerlevel) {
						if ($row[0] != $powerlevel) {
							$query = "UPDATE $table SET powerlevel='$powerlevel' WHERE email='$email'";
							$result = mysqli_query($dbc, $query);
							if (mysqli_affected_rows($dbc) == 1) {
								$msg .= get_msg('update_success', $email, $row[0], $powerlevel);
							} else {
								$msg .= get_msg('update_failed', $email, $row[0], $powerlevel);
							}
						}
					} else {
						$msg .= $pwlvltablephp['msg']['err_priv_unmet'];
					}
				} else {
					$msg .= $pwlvltablephp['msg']['err_not_found'];
				}
			} else {
				$msg .= $pwlvltablephp['msg']['err_priv_unmet'];
			}
		}
	}
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>

<h1><?=$pwlvltablephp['h1_title']; ?></h1>

<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color:red;\">$msg</p>";
        }
?>

<?php
	$query = "SELECT user_id, email, powerlevel FROM $table WHERE powerlevel != 1";
	$result = @mysqli_query($dbc, $query);
	$num = mysqli_num_rows($result);

	if ($num > 0) {
		echo "<h3>{$pwlvltablephp['user_num_msg'][0]} $num</h3>";
		echo "<form name=\"pwlvltable\" action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\">";
		echo "<table style=\"text-align:center; padding: 2px;\">
		<tr>
			<th style=\"width: 0.2%;\">&nbsp;</td>
			<th style=\"width: 1.5%;\">{$pwlvltablephp['th_pwlvltable']['id']}</td>
			<th style=\"width: 5%;\">{$pwlvltablephp['th_pwlvltable']['email']}</td>
			<th style=\"width: 5%;\">{$pwlvltablephp['th_pwlvltable']['powerlevel']}</td>
		</tr>
		";
		while ($row = mysqli_fetch_row($result)) {
			if ($row[2] >= $mypowerlevel) {
				echo "
				<tr>
					<td style=\"width: 0.2%;\">&nbsp;</td>
					<td style=\"width: 1.5%;\">$row[0]</td>
					<td style=\"width: 5%;\"><a href=\"/profiles.php/{$row[1]}\">$row[1]</a></td>
					<td style=\"width: 5%;\">$row[2]</td>
				</tr>
				\n";
			} else {
				echo "
				<tr>
					<td style=\"width: 0.2%;\">&nbsp;</td>
					<td style=\"width: 1.5%;\">$row[0]</td>
					<td style=\"width: 5%;\"><a href=\"/profiles.php?email={$row[1]}\">$row[1]</a></td>
					<td style=\"width: 5%;\"><input type=\"text\" name=\"powerlevel[{$row[1]}]\" value=\"{$row[2]}\" size=\"1\" maxlength=\"3\"></td>
				</tr>
				\n";
			}
		}
		echo '</table>';
		echo "<br><div style=\"text-align:center;\"><input type=\"submit\" name=\"update_power\" value=\"{$pwlvltablephp['input']['update_power']}\"></div>";
		echo '</form>';
		mysqli_free_result($result);
	} else {
		echo "<p>{$pwlvltablephp['user_num_msg'][1]}</p>";
	}

	mysqli_close($dbc);
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
