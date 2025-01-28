<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
if (empty($_GET['id'])) {
	header("Location: $protocol://$server/index.php");
	exit;
}
$id = $_GET['id'];
$result = mysqli_execute_query($dbc, "SELECT * FROM $msgtable WHERE msg_id=?", [$id]);
if (mysqli_num_rows($result) != 1) {
	header("Location: $protocol://$server/index.php");
	exit;
}
$assoc = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if ($assoc['r_pwlvl'] > 0 && !isset($_SESSION['auth'])) {
	/* unreadable for "normal" also unreadable for anonymous users */
	header("Location: $protocol://$server/account/login.php");
	exit;
}
foreach ($assoc as $k => $value) {
	$assoc[$k] = export_data($value);
}
/*
 * Groups are separated extensively so admins can config how they want
 * links to displays.
 * \\0 full url for sure.
 * \\3 for hostname
 * \\9 for path
 * \\2 for the protocol, \\1 for protocol with ://
 */
$urlre = "((http{1}s?):\/\/)" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]){2,6}"
. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.:~?@\-\/]*)?";

$body = nl2br(preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>',
	      str_replace("&amp;", "&", $assoc['body'])), false);

$author = get_user_info("WHERE email=?", "name, powerlevel",
			[$assoc['from_addr']]);
?>
<?php
$title = $assoc['subject'];
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
if ($assoc['r_pwlvl'] <= $_SESSION['powerlevel'])
{
?>
<h1><?=$assoc['subject']; ?></h1>
<p><?php echo "Boi " . 
"<a href=\"$protocol://$server/profiles.php?email={$assoc['from_addr']}\">" .
"{$author['name']}</a> " .
"&lt;<a href=\"mailto:{$assoc['from_addr']}\">{$assoc['from_addr']}</a>&gt; ";
?></p>
<?php
if ($assoc['relate_to'] != 0) {
	echo "<p><a href=\"$protocol://$server/view.php?id={$assoc['relate_to']}\">Tin nhan truoc</a></p>";
}
?>
<pre><a href="<?php echo "$protocol://$server{$_SERVER['REQUEST_URI']}"; ?>"><?=$assoc['last_edit'];?></a></pre>
<?php
if ($assoc['w_pwlvl'] <= $_SESSION['powerlevel']) {
echo "
<p><a href=\"$protocol://$server/board.php?relate_to=$id\">Viet tra loi</a></p>
";
}
?>
<p><br><?=$body;?></p>
<hr>
<?php
$rmsg_result = mysqli_execute_query($dbc, "SELECT * FROM $msgtable "
	     . "WHERE relate_to=? AND r_pwlvl <= '{$_SESSION['powerlevel']}' "
	     . "AND to_addr='{$assoc['to_addr']}' ORDER BY last_edit DESC", [$id]);
$rmsg_count = mysqli_num_rows($rmsg_result);
echo "<h3>Tra loi ($rmsg_count)</h3>";
if ($rmsg_count > 0) {
	while ($rmsg = mysqli_fetch_assoc($rmsg_result)) {
		echo "<h4>{$rmsg['subject']}</h4>";
		echo "<p>Boi {$rmsg['from_addr']}<br></p>";
		echo "<p>{$rmsg['body']}</p>";
		echo "<pre><a href=\"$protocol://$server/view.php?" .
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
