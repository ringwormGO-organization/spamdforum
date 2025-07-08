<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
require_once("inc/view_inc.php");
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
foreach ($msginfo as $k => $value)
	/* shut up php deprecated warning */
	$msginfo[$k] = is_null($msginfo[$k]) ? $value : export_data($value);
/* This is a HACK to enable ! for code formatting! */
$body = format_body("<p>" . $msginfo['body'] . "\n</p>");

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
	   . "{$msginfo['name']}</a>";
else
	echo "<a href=\"mailto:{$msginfo['from_addr']}\">"
	   . "{$msginfo['from_addr']}</a>";
?>
</p>
<?=$body;?>
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
		foreach ($rmsg as $k => $value)
			$rmsg[$k] = is_null($value) ? $value : export_data($value);
		echo "<h4>";
		if (!empty($rmsg['name'])) {
			echo "<a href=\"/profiles.php?email="
			   . "{$rmsg['from_addr']}\">{$rmsg['name']}</a>: ";
		} else {
			echo "<a href=\"mailto:{$rmsg['from_addr']}\">"
			   . "{$rmsg['from_addr']}</a>: ";
		}
		echo "{$rmsg['subject']}</h4>\n";
		echo "<p>" . format_body($rmsg['body']) . "</p>\n";
		echo "<p><b>{$rmsg['votes']}</b></p>\n";
		echo "<pre><a href=\"$protocol://$server/forum/index.php?"
		   . "id={$rmsg['msg_id']}\">{$rmsg['created_at']}</a></pre>\n\n";
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
