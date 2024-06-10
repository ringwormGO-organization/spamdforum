<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>

<?php
$need_msg = FALSE;
$msg = NULL;

$mypwlvl = $_SESSION['powerlevel'];

if (isset($_POST['update_power']) && isset($_POST['powerlevel'])) {
	$need_msg = TRUE;
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
	foreach($_POST['powerlevel'] as $email => $powerlevel) {
		$email = escape_data($email);
		$powerlevel = intval(escape_data($powerlevel));
		if (! ($powerlevel <= $mypwlvl)) {
			$msg .= $pwphp['msg']['err_priv_unmet'];
			goto stop;
		}
		$query = "SELECT powerlevel FROM $table WHERE email=?";
		$result = mysqli_execute_query($dbc, $query, [$email]);
		if (mysqli_num_rows($result) < 1) {
			$msg .= $pwphp['msg']['err_not_found'];
			goto stop;
		}
		$row = mysqli_fetch_row($result);
		if ($row[0] >= $mypwlvl) {
			$msg .= $pwphp['msg']['err_priv_unmet'];
			goto stop;
		}
		if ($row[0] != $powerlevel) {
			$query = "UPDATE $table SET powerlevel='$powerlevel' WHERE email=?";
			$result = mysqli_execute_query($dbc, $query, [$email]);
			if (mysqli_affected_rows($dbc) == 1) {
				$msg .= sprintf($pwphp['msg']['update_success'], $email, $row[0], $powerlevel);
			} else {
				$msg .= sprintf($pwphp['msg']['update_failed'], $email, $row[0], $powerlevel);
			}
		}
	}
}
?>
<?php
stop:
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<h1><?=$pwphp['h1_title']; ?></h1>
<?php
if (isset($msg)) {
	$msg = nl2br($msg, false);
	echo "<p style=\"color:red;\">$msg</p>";
        }
?>
<?php
$query = "SELECT user_id, email, powerlevel FROM $table WHERE powerlevel != ?";
$result = mysqli_execute_query($dbc, $query, [0]);
$num = mysqli_num_rows($result);

if ($num > 0) {
	echo "<h3>" . sprintf($pwphp['user_num_msg'][0], $num) . "</h3>" .
	"<form name=\"pwlvltable\" action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\">" .
	"<table style=\"text-align:center; padding: 2px;\">
	<tr>
	<th style=\"width: 0.2%;\">&nbsp;</th>
	<th style=\"width: 1.5%;\">{$pwphp['th_pwlvltable']['id']}</th>
	<th style=\"width: 5%;\">{$pwphp['th_pwlvltable']['email']}</th>
	<th style=\"width: 5%;\">{$pwphp['th_pwlvltable']['powerlevel']}</th>
	</tr>
	";
	while ($row = mysqli_fetch_row($result)) {
		foreach ($row as $key => $value) {
			$row[$key] = export_data($value);
		}
		echo "
		<tr>
		<td style=\"width: 0.2%;\">&nbsp;</td>
		<td style=\"width: 1.5%;\">$row[0]</td>
		<td style=\"width: 5%;\"><a href=\"/profiles.php/{$row[1]}\">$row[1]</a></td>
		";
		if ($row[2] >= $mypwlvl) {
			echo "<td style=\"width: 5%;\">$row[2]</td>";
		} else {
			echo "<td style=\"width: 5%;\"><input type=\"text\" name=\"powerlevel[{$row[1]}]\" value=\"{$row[2]}\" size=\"1\" maxlength=\"3\"></td>";
		}
		echo "
		</tr>
		\n";
	}
	echo '</table>';
	echo "<div style=\"text-align:center;\"><input type=\"submit\" name=\"update_power\" value=\"{$pwphp['input']['update_power']}\"></div>";
	echo '</form>';
	mysqli_free_result($result);
} else {
	echo "<p>{$pwphp['user_num_msg'][1]}</p>";
}

mysqli_close($dbc);
?>

<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
