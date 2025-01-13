<?php
/* See file COPYING for permissions and conditions to use the file. */
$title = "Trang chu";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
echo "<h1>Cac bai viet</h1>";
$result = mysqli_query($dbc, "SELECT * FROM $msgtable WHERE relate_to=0 AND " .
	  "r_pwlvl<={$_SESSION['powerlevel']} ORDER BY last_edit DESC");
if (!$result)
	goto footer;
while ($curmsg = mysqli_fetch_assoc($result)) {
	echo "<h3><a href=\"$protocol://$server/view.php?id=".
		"{$curmsg['msg_id']}\">{$curmsg['subject']}</a></h3>";
	echo "<pre>boi {$curmsg['from_addr']} ngay {$curmsg['last_edit']}</pre>";
}

?>
<?php
footer:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
