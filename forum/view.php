<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
if (!isset($id))
	exit;
$assoc = get_msg_info("WHERE msg_id=?", "*", [$id]);
if (!$assoc) {
	header("Location: $protocol://$server/forum/");
	exit;
}
if ($assoc['r_pwlvl'] > 0 && !isset($_SESSION['auth'])) {
	/* unreadable for "normal" also unreadable for anonymous users */
	header("Location: $protocol://$server/account/login.php");
	exit;
}
$author = get_user_info("WHERE email=?", "name, powerlevel",
			[$assoc['from_addr']]);
foreach ($assoc as $k => $value) {
	$assoc[$k] = export_data($value);
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
	      $assoc['body']), false);
?>
<?php
$title = $assoc['subject'];
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
if ($assoc['r_pwlvl'] <= $_SESSION['powerlevel'])
{
?>
<h1><?=$assoc['subject']; ?></h1>
<?php
if ($assoc['relate_to'] != 0) {
	echo "<p><a href=\"$protocol://$server/forum/index.php?id="
	   . "{$assoc['relate_to']}\">(Tin nhan truoc)</a></p>\n";
}
?>
<p><?php echo "{$assoc['votes']}| ";
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
if (isset($author['name']))
	echo "Boi <a href=\"/profiles.php?email={$assoc['from_addr']}\">"
	   . "{$author['name']}</a>\n";
else
	echo "<a href=\"mailto:{$assoc['from_addr']}\">"
	   . "{$assoc['from_addr']}</a>\n";
?>
</p>
<pre><a href="<?php echo "$protocol://$server{$_SERVER['REQUEST_URI']}"; ?>"><?=$assoc['last_edit'];?></a></pre>
<p><br><?=$body;?></p>
<hr>
<?php
$rmsg_result = mysqli_execute_query($dbc, "SELECT * FROM $msgtable "
	     . "WHERE relate_to=? AND r_pwlvl <= '{$_SESSION['powerlevel']}' "
	     . "AND to_addr='{$assoc['to_addr']}' ORDER BY last_edit DESC", [$id]);
$rmsg_count = mysqli_num_rows($rmsg_result);
echo "<h3>$rmsg_count nhan xet</h3>";
if ($assoc['w_pwlvl'] <= $_SESSION['powerlevel']) {
	echo "<p><a href=\"$protocol://$server/forum/board.php?relate_to=$id\">"
	   . "Viet tra loi</a></p>";
}
if ($rmsg_count > 0) {
	while ($rmsg = mysqli_fetch_assoc($rmsg_result)) {
		foreach ($rmsg as $k => $value) {
			$rmsg[$k] = export_data($value);
		}
		echo "<h4>{$rmsg['subject']}</h4>";
		echo "<p>Boi {$rmsg['from_addr']}<br></p>";
		echo "<p>" . nl2br($rmsg['body'], false) . "</p>";
		echo "<pre><a href=\"$protocol://$server/forum/index.php?" .
		     "id={$rmsg['msg_id']}\">{$rmsg['last_edit']}</a>

</pre>";
	}
}
?>
<?php
} else {
	echo "<h3>Ban khong co quyen truy cap vao noi dung nay.</h3>";
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
