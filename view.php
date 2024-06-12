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
$assoc = mysqli_fetch_assoc(mysqli_execute_query($dbc, "SELECT * FROM $msgtable WHERE msg_id=?", [$id]));
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
. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.~?@\-\/]*)";

$body = nl2br(preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>',
	      str_replace("&amp;", "&", $assoc['body'])));

$author = mysqli_fetch_assoc(mysqli_execute_query($dbc, "SELECT name, powerlevel FROM $table WHERE email='{$assoc['from_addr']}'"));
?>
<?php
$title = $assoc['subject'];
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
if (!isset($_SESSION['auth']) || $assoc['r_pwlvl'] <= $_SESSION['powerlevel'])
{
?>
<h1><?=$assoc['subject']; ?></h1>
<p><?php echo "Boi " . 
"<a href=\"$protocol://$server/profiles.php?email={$assoc['from_addr']}\">" .
"{$author['name']}</a> " .
"&lt;<a href=\"mailto:{$assoc['from_addr']}\">{$assoc['from_addr']}</a>&gt; ";
?></p>
<p>chinh sua lan cuoi: <?=$assoc['last_edit'];?></p>
<p><a href="<?php echo "$protocol://$server/board.php?relate_to=$id"; ?>">Viet tra loi</a></p>
<p><?=$body;?></p>
<hr>
<h3>Tra loi</h3>
<?php
} else {
	echo "<h3>Ban khong co quyen truy cap vao noi dung nay.</h3>";
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
