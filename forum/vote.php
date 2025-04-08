<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
function get_msg_votes($msg_id, $uid) {
	global $dbc, $msgtable, $votetable;
	$query = "SELECT r_pwlvl, votes, amount FROM $msgtable LEFT JOIN "
		. "$votetable ON $votetable.msg_id=? AND forum_votes.author=? "
		. "WHERE forum_msg.msg_id=?";
	$arr = [$msg_id, $uid, $msg_id];
	$msginfo = mysqli_fetch_assoc(mysqli_execute_query($dbc, $query, $arr));
	return $msginfo;
}
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
	$myuid = $_SESSION['user_id'];
	$target = get_msg_votes($id, $myuid);
	if ($target['r_pwlvl'] > $_SESSION['powerlevel'])
		goto end;
	if (isset($target['amount']))
		$cur_amount = intval($target['amount']);
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
	if ($target['amount'] === NULL)
		mysqli_execute_query($dbc, $ins_query, [$id, $myuid, $amount]);
	else
		mysqli_execute_query($dbc, $update_query, [$amount, $id]);
	mysqli_execute_query($dbc, $update_msg, [$newval, $id]);
}

end:
header("Location: $protocol://$server/forum/index.php?id=$id");
exit;
?>
