<?php
/* See file COPYING for permissions and conditions to use the file. */
require("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
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
$title = $words['msg_list'];
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
$start = hrtime(true);
echo "<h1>{$words['msg_list']}</h1>\n";
$result = mysqli_execute_query($dbc, "SELECT fm.msg_id, fm.subject, "
	    . "fm.from_addr, fm.r_pwlvl, fm.w_pwlvl, fm.votes, fm.created_at, "
	    . "name, COUNT(fm2.msg_id) AS ncmt FROM $table, $msgtable AS fm "
	    . "LEFT JOIN $msgtable fm2 ON fm2.relate_to=fm.msg_id "
	    . "WHERE fm.relate_to=0 AND fm.r_pwlvl<=? AND fm.votes>-5 "
	    . "AND fm.from_addr=$table.email GROUP BY fm.msg_id "
	    . "ORDER BY votes DESC, created_at DESC",
	    [$_SESSION['powerlevel']]);
if (!$result)
	goto footer;
while ($curmsg = mysqli_fetch_assoc($result)) {
	foreach ($curmsg as $k => $value)
		$curmsg[$k] = export_data($value);
	echo "<h3><a href=\"$protocol://$server/forum/index.php?id=".
		"{$curmsg['msg_id']}\">{$curmsg['subject']}</a></h3>\n";
	echo "<pre>{$curmsg['created_at']} {$words['from']} ";
	if ($curmsg['name']) {
		echo "<a href=\"/profiles.php?email={$curmsg['from_addr']}\">"
		   . "{$curmsg['name']}</a>\n\n\n";
	} else {
		echo "<a href=\"mailto:{$curmsg['from_addr']}\">"
		   . "{$curmsg['from_addr']}</a>\n\n\n";
	}
	echo "<b>{$curmsg['votes']}</b>  {$curmsg['ncmt']} {$words['comment']}";
	if ($curmsg['r_pwlvl'] > 0)
		echo " (r={$curmsg['r_pwlvl']})";
	if ($curmsg['w_pwlvl'] > 0)
		echo " (w={$curmsg['w_pwlvl']})";
	echo "</pre>\n<hr>\n\n";
}
echo "<p>" . hrtime(true) - $start . "</p>\n";
?>
<?php
footer:
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
