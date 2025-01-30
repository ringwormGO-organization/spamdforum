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
/*
if ($id != 0) {
	include("{$_SERVER['DOCUMENT_ROOT']}/forum/view.php");
	exit;
}
*/
include("{$_SERVER['DOCUMENT_ROOT']}/forum/view.php");
exit;
listmsg:
$title = "Cac bai viet";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
echo "<h1>Cac bai viet</h1>\n";
$result = mysqli_query($dbc, "SELECT * FROM $msgtable WHERE relate_to=0 AND " .
	  "r_pwlvl<={$_SESSION['powerlevel']} ORDER BY last_edit DESC");
if (!$result)
	goto footer;
while ($curmsg = mysqli_fetch_assoc($result)) {
	echo "<h3><a href=\"$protocol://$server/forum/index.php?id=".
		"{$curmsg['msg_id']}\">{$curmsg['subject']}</a></h3>\n";
	echo "<pre>boi {$curmsg['from_addr']} ngay {$curmsg['last_edit']}"
	   . "</pre>\n\n";
}

?>
<?php
footer:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
