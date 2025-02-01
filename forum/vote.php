<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
$id = 0;
if (!empty($_GET['id']) && isset($_GET['amount'])) {
	$id = intval($_GET['id']);
	$amount = intval($_GET['amount']);
	if ($id == 0)
		goto end;
	if ($amount > 1 || $amount < -1)
		goto end;
	if (!$auth || $_SESSION['powerlevel'] < 0)
		goto end;
	$target = get_msg_info("WHERE msg_id=?", "r_pwlvl, votes", [$id]);
	if ($target['r_pwlvl'] > $_SESSION['powerlevel'])
		goto end;
	$myuid = $_SESSION['user_id'];
	$myvoteq = "SELECT amount FROM $votetable WHERE msg_id=? AND "
		 . "author=?";
	$myvote = mysqli_fetch_row(mysqli_execute_query($dbc, $myvoteq,
				   [$id, $myuid]));
	if (isset($myvote[0]))
		$cur_amount = intval($myvote[0]);
	else
		$cur_amount = 0;
	$change = $amount - $cur_amount;
	if ($change == 0)
		goto end;

	$newval = $target['votes'] + $change;
	$ins_query = "INSERT INTO $votetable (msg_id, author, amount) "
		   . "VALUES (?, ?, ?)";
	$update_query = "UPDATE $votetable SET amount=? WHERE msg_id=?";
	$update_msg = "UPDATE $msgtable SET votes=? WHERE msg_id=?";
	if (!isset($myvote[0]))
		mysqli_execute_query($dbc, $ins_query, [$id, $myuid, $amount]);
	else
		mysqli_execute_query($dbc, $update_query, [$amount, $id]);
	mysqli_execute_query($dbc, $update_msg, [$newval, $id]);
}

end:
header("Location: $protocol://$server/forum/index.php?id=$id");
exit;
?>
