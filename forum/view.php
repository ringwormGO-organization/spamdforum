<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
if (!isset($id))
	exit;
$msginfo = get_msg_info("WHERE msg_id=? AND $table.email=$msgtable.from_addr",
			"*", [$id]);
if (!$msginfo) {
	header("Location: $protocol://$server/forum/");
	exit;
}
foreach ($msginfo as $k => $value) {
	$msginfo[$k] = export_data($value);
}
/*
 * Groups are separated extensively so admins can config how they want
 * links to displays.
 * \\0 full url.
 * \\3 for hostname
 * \\9 for path
 * \\2 for the protocol, \\1 for protocol with ://
 */
$urlre = "((http{1}s?):\/\/)" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]){2,6}"
. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.:~?@\-\/]*)?";

$body = nl2br(preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>',
	      $msginfo['body']), false);
?>
<?php
$title = $msginfo['subject'];
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
if ($msginfo['r_pwlvl'] <= $_SESSION['powerlevel'] ||
    $msginfo['from_addr'] == $_SESSION['email'])
{
?>
<h1><?=$msginfo['subject']; ?></h1>
<?php
if ($msginfo['relate_to'] != 0) {
	echo "<p><a href=\"$protocol://$server/forum/index.php?id="
	   . "{$msginfo['relate_to']}\">(Tin nhan truoc)</a></p>\n";
}
?>
<p><?php echo "{$msginfo['votes']} | \n";
if ($auth && $_SESSION['powerlevel'] >= 0) {
$q = "SELECT amount FROM $votetable WHERE msg_id=? AND author=?";
$myuid = $_SESSION['user_id'];
$myvote = mysqli_fetch_row(mysqli_execute_query($dbc, $q, [$id, $myuid]));
if (!isset($myvote[0]) || $myvote[0] == 0) {
	echo "<a href=\"/forum/vote.php?id=$id&&amount=1\">+1 </a>\n";
	echo "<a href=\"/forum/vote.php?id=$id&&amount=-1\">-1</a>\n";
} else {
	if ($myvote[0] == 1) {
		echo "<a href=\"/forum/vote.php?id=$id&&amount=0\">+0 </a>\n";
		echo "<a href=\"/forum/vote.php?id=$id&&amount=-1\">-1</a>\n";
	} else {
		echo "<a href=\"/forum/vote.php?id=$id&&amount=1\">+1 </a>\n";
		echo "<a href=\"/forum/vote.php?id=$id&&amount=0\">-0</a>\n";
	}
}
}
if (isset($msginfo['name']))
	echo "Boi <a href=\"/profiles.php?email={$msginfo['from_addr']}\">"
	   . "{$msginfo['name']}</a>\n";
else
	echo "<a href=\"mailto:{$msginfo['from_addr']}\">"
	   . "{$msginfo['from_addr']}</a>\n";
?>
</p>
<?php
if ($msginfo['from_addr'] == $_SESSION['email'] || $_SESSION['powerlevel'] >= 50)
	echo "<p><a href=\"/forum/board.php?editid={$msginfo['msg_id']}\">"
	   . "Chinh sua</a></p>\n";
?>
<pre><a href="<?="$protocol://$server{$_SERVER['REQUEST_URI']}";
	   ?>"><?=$msginfo['created_at'];?></a></pre>
<p><?=$body;?></p>
<hr>
<?php
$rmsg_result = mysqli_execute_query($dbc, "SELECT * FROM $msgtable "
	     . "WHERE relate_to=? AND r_pwlvl <= '{$_SESSION['powerlevel']}' "
	     . "AND to_addr='{$msginfo['to_addr']}' ORDER BY last_edit DESC", [$id]);
$rmsg_count = mysqli_num_rows($rmsg_result);
echo "<h3>$rmsg_count nhan xet</h3>\n";
if ($auth && $msginfo['w_pwlvl'] <= $_SESSION['powerlevel']) {
	echo "<p><a href=\"$protocol://$server/forum/board.php?relate_to=$id\">"
	   . "Viet nhan xet</a></p>\n";
}
if ($rmsg_count > 0) {
	while ($rmsg = mysqli_fetch_assoc($rmsg_result)) {
		$author = get_user_info("WHERE email=?", "name",
			  [$rmsg['from_addr']]);
		foreach ($rmsg as $k => $value) {
			$rmsg[$k] = export_data($value);
		}
		echo "<h4>";
		if (!empty($author['name'])) {
			echo "<a href=\"/profiles.php?email="
			   . "{$rmsg['from_addr']}\">{$author['name']}</a>: ";
		} else {
			echo "<a href=\"mailto:{$rmsg['from_addr']}\">"
			   . "{$rmsg['from_addr']}</a>: ";
		}
		echo "{$rmsg['subject']}</h4>\n";
		echo "<p>" . nl2br($rmsg['body'], false) . "</p>\n";
		echo "<pre><a href=\"$protocol://$server/forum/index.php?"
		   . "id={$rmsg['msg_id']}\">{$rmsg['created_at']}</a></pre>\n";
	}
}
?>
<?php
} else {
	echo "<h3>Ban khong co quyen truy cap vao noi dung nay.</h3>";
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
