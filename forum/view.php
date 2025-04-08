<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");

if (!isset($id))
	exit;
$start = hrtime(true);
if ($auth) {
	$uid = $_SESSION['user_id'];
	$msginfo = get_msg_info("LEFT JOIN $table ON $table.email=$msgtable.from_addr "
	. "LEFT JOIN $votetable ON $votetable.msg_id=? AND $votetable.author=? "
	. "WHERE $msgtable.msg_id=?", "*", [$id, $uid,$id]);
} else {
	$msginfo = get_msg_info("LEFT JOIN $table ON $table.email=$msgtable.from_addr "
			. "WHERE msg_id=?"
			. "", "*", [$id]);
}
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
$urlre = "((http{1}s?):\/\/)" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]){1,6}"
. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.:~?@\-\/]*)?";

$body = nl2br(preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>',
	      $msginfo['body']), false);
?>
<?php
if ($msginfo['r_pwlvl'] <= $_SESSION['powerlevel'] ||
    $msginfo['from_addr'] == $_SESSION['email'])
{
	$words['page_title'] = $msginfo['subject'];
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<h1><?=$msginfo['subject']; ?></h1>
<?php
if ($msginfo['relate_to'] != 0) {
	echo "<p><a href=\"$protocol://$server/forum/index.php?id="
	   . "{$msginfo['relate_to']}\">({$words['previous_msg']})</a></p>\n";
}
?>
<p><?php
if (isset($msginfo['name']))
	echo "<a href=\"/profiles.php?email={$msginfo['from_addr']}\">"
	   . "{$msginfo['name']}</a>\n";
else
	echo "<a href=\"mailto:{$msginfo['from_addr']}\">"
	   . "{$msginfo['from_addr']}</a>\n";
?>
</p>
<p><?=$body;?>
</p>
<?php
if ($msginfo['from_addr'] == $_SESSION['email'] ||
    $_SESSION['powerlevel'] >= 50) {
	echo "<p><a href=\"/forum/board.php?editid=$id\">"
	   . "{$words['edit']}</a></p>\n";
}
echo "<p><b>{$msginfo['votes']}</b> | \n";
if ($auth && $_SESSION['powerlevel'] >= 0) {
	$myvote = $msginfo['amount'];
	if (!$myvote) {
		echo "<a href=\"/forum/vote.php?id=$id&amount=1\">+1 </a>\n";
		echo "<a href=\"/forum/vote.php?id=$id&amount=-1\">-1</a>\n";
	} else {
		if ($myvote == 1) {
			echo "<a href=\"/forum/vote.php?id=$id&amount=0\">+0 </a>\n";
			echo "<a href=\"/forum/vote.php?id=$id&amount=-1\">-1</a>\n";
		} else {
			echo "<a href=\"/forum/vote.php?id=$id&amount=1\">+1 </a>\n";
			echo "<a href=\"/forum/vote.php?id=$id&amount=0\">-0</a>\n";
		}
	}
}
?>
</p>
<pre><a href="<?="$protocol://$server{$_SERVER['REQUEST_URI']}";
	   ?>"><?=$msginfo['created_at'];?></a></pre>
<hr>
<?php
$rmsg_result = mysqli_execute_query($dbc, "SELECT * FROM $msgtable LEFT JOIN "
	     . "$table ON $table.email=$msgtable.from_addr WHERE relate_to=? "
	     . "AND r_pwlvl<=? ORDER BY votes DESC",
		[$id, $_SESSION['powerlevel']]);
$rmsg_count = mysqli_num_rows($rmsg_result);
if ($auth && $msginfo['w_pwlvl'] <= $_SESSION['powerlevel']) {
	echo "<p><a href=\"$protocol://$server/forum/board.php?relate_to=$id\">"
	   . "{$words['write_comment']}</a></p>\n";
}
echo "<h3>$rmsg_count {$words['comment']}</h3>\n";
if ($rmsg_count > 0) {
	while ($rmsg = mysqli_fetch_assoc($rmsg_result)) {
		foreach ($rmsg as $k => $value) {
			$rmsg[$k] = export_data($value);
		}
		echo "<h4>";
		if (!empty($rmsg['name'])) {
			echo "<a href=\"/profiles.php?email="
			   . "{$rmsg['from_addr']}\">{$rmsg['name']}</a>: ";
		} else {
			echo "<a href=\"mailto:{$rmsg['from_addr']}\">"
			   . "{$rmsg['from_addr']}</a>: ";
		}
		echo "{$rmsg['subject']}</h4>\n";
		echo "<p>" . nl2br(preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>',
			$rmsg['body']), false) . "\n</p>\n";
		echo "<p><b>{$rmsg['votes']}</b></p>\n";
		echo "<pre><a href=\"$protocol://$server/forum/index.php?"
		   . "id={$rmsg['msg_id']}\">{$rmsg['created_at']}</a></pre>\n";
	}
}
?>
<?php
} else {
	$words['page_title'] = $words['err_perm'];
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
	echo "<h3>{$words['err_perm']}</h3>";
}
echo "<p>" . hrtime(true) - $start . "</p>\n";
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
