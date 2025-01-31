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
$result = mysqli_query($dbc, "SELECT * FROM $msgtable WHERE relate_to=0 AND "
	. "r_pwlvl<={$_SESSION['powerlevel']} ORDER BY votes DESC, "
	. "created_at DESC");
if (!$result)
	goto footer;
while ($curmsg = mysqli_fetch_assoc($result)) {
	foreach ($curmsg as $k => $value)
		$curmsg[$k] = export_data($value);
	$cid = $curmsg['msg_id'];
	$ncmt_q = "SELECT COUNT(*) FROM forum_msg WHERE relate_to=?";
	$ncmt = mysqli_fetch_row(mysqli_execute_query($dbc, $ncmt_q, [$cid]));
	echo "<h3><a href=\"$protocol://$server/forum/index.php?id=".
		"{$curmsg['msg_id']}\">{$curmsg['subject']}</a></h3>\n";
	echo "<pre>{$curmsg['created_at']} tu {$curmsg['from_addr']}\n\n\n"
	   . "<b>{$curmsg['votes']}</b>  $ncmt[0] nhan xet</b></pre>\n<hr>";
}

?>
<?php
footer:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
