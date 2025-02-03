<?php
/* See file COPYING for permissions and conditions to use the file. */
if (empty($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == '/') {
	if (!empty($_GET['id'])) {
		$id = intval($_GET['id']);
	} else {
		goto listmsg;
		exit;
	}
} else {
	$id = intval(substr($_SERVER['PATH_INFO'], 1));
}
include("{$_SERVER['DOCUMENT_ROOT']}/forum/view.php");
exit;
listmsg:
$title = "Cac bai viet";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
echo "<h1>Cac bai viet</h1>\n";
$result = mysqli_execute_query($dbc, "SELECT msg_id, subject, from_addr, "
	    . "r_pwlvl, w_pwlvl, votes, created_at, name FROM $msgtable, "
	    . "$table WHERE relate_to=0 AND r_pwlvl<=? AND votes > -5 "
	    . "AND $msgtable.from_addr=$table.email "
	    . "ORDER BY votes DESC, created_at DESC",
	    [$_SESSION['powerlevel']]);
if (!$result)
	goto footer;
while ($curmsg = mysqli_fetch_assoc($result)) {
	foreach ($curmsg as $k => $value)
		$curmsg[$k] = export_data($value);
	$cid = $curmsg['msg_id'];
	$ncmt_q = "SELECT COUNT(*) FROM forum_msg WHERE relate_to=? "
		. "AND r_pwlvl<=?";
	$ncmt = mysqli_fetch_row(mysqli_execute_query($dbc, $ncmt_q,
		[$cid, $_SESSION['powerlevel']]));
	echo "<h3><a href=\"$protocol://$server/forum/index.php?id=".
		"{$curmsg['msg_id']}\">{$curmsg['subject']}</a></h3>\n";
	echo "<pre>{$curmsg['created_at']} tu ";
	if ($curmsg['name']) {
		echo "<a href=\"/profiles.php?email={$curmsg['from_addr']}\">"
		   . "{$curmsg['name']}</a>\n\n\n";
	} else {
		echo "<a href=\"mailto:{$curmsg['from_addr']}\">"
		   . "{$curmsg['from_addr']}</a>\n\n\n";
	}
	echo "<b>{$curmsg['votes']}</b>  $ncmt[0] nhan xet";
	if ($curmsg['r_pwlvl'] > 0)
		echo " (r={$curmsg['r_pwlvl']})";
	if ($curmsg['w_pwlvl'] > 0)
		echo " (w={$curmsg['w_pwlvl']})";
	echo "</pre>\n<hr>\n\n";
}

?>
<?php
footer:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
