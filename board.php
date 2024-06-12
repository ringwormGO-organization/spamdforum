<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
if (!isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/account/login.php");
	exit;
}
if (!isset($_POST['send'])) {
	goto html;
}
$good_chars = "/[\x20-\x7F\x{00C0}-\x{1EF9}]/iu";
/* Alphanumeric plus Vietnamese characters plus some other character idk */
if (!empty($_POST['to'])) {
	$to = escape_data($_POST['to']);
} else {
	$to = FALSE;
	$msg .= "Nhap vao chu de hoac email nguoi dung!\n";
}
if (!empty($_POST['subject']) && strlen($_POST['subject']) < 256 &&
    preg_match($good_chars, $_POST['subject'])) {
	$subject = escape_data($_POST['subject']);
} else {
	$subject = FALSE;
	$msg .= "Chu de tin nhan khong hop le.\n";
}
if (!empty($_POST['body']) && strlen($_POST['body']) < 32768 &&
    preg_match($good_chars, $_POST['body'])) {
	$body = escape_data($_POST['body']);
} else {
	$body = FALSE;
	$msg .= "Tin nhan khong hop le.\n";
}
/* "Muted" users can view any msg, but not write */
$r_pwlvl = -1;
$w_pwlvl = 0;
if (!empty($_POST['r_pwlvl'])) {
	if (($r_pwlvl = intval($_POST['r_pwlvl'])) > $_SESSION['powerlevel']) {
		/*
		 * Creating msgs that the author can't even access
		 * is disallowed.
		 */
		$r_pwlvl = $_SESSION['powerlevel'];
	}
}
if (!empty($_POST['w_pwlvl'])) {
	if (($w_pwlvl = intval($_POST['w_pwlvl'])) > $_SESSION['powerlevel']) {
		/* As well as msgs that author can't write to. */
		$w_pwlvl = $_SESSION['powerlevel'];
	}
}
if (! ($to && $subject && $body)) {
	$msg .= "Hay thu lai.\n";
	goto html;
}
$from = $_SESSION['email'];
$query = "INSERT INTO $msgtable (subject, body, from_addr, to_addr, "
	. "r_pwlvl, w_pwlvl, last_edit) VALUES (?, ?, ?, ?, ?, ?, NOW())";
if (mysqli_execute_query($dbc, $query, [$subject, $body, $from, $to,
			 $r_pwlvl, $w_pwlvl])) {
	/* Redirect to the new msg if success */
	$id = mysqli_insert_id($dbc);
	header("Location: $protocol://$server/view.php?id=$id");
	exit;
} else {
	$msg .= "May chu hien dang gap truc trac. Xin loi vi su co nay.\n";
}
mysqli_close($dbc);
?>
<?php
$title = "Viet tin nhan";
html:
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">$msg</p>";
}
?>
<fieldset>
<legend><b>Viet tin nhan</b></legend>
<form name="newmsg" action="<?=$_SERVER['PHP_SELF']; ?>" method="POST">
<table style="border-width:0; width:100%;">
<tr>
	<td><b>To:</b></td>
	<td><input type="text" name="to" size="64" maxlength="128"></td>
</tr>
<tr>
	<td><b>Subject:</b></td>
	<td><input type="text" name="subject" size="64" maxlength="255"></td>
</tr>
<tr>
	<td><b>R/W level:</b></td>
	<td><input type="text" name="r_pwlvl" size="3" maxlength="3" value="-1">
	<input type="text" name="w_pwlvl" size="3" maxlength="3" value="0"></td>
</tr>
<tr>
	<td><b>Relate to:</b></td>
	<td><input type="text" name="relate_to" size="15" value="<?php if (isset($_GET['relate_to'])) {echo intval($_GET['relate_to']);} else {echo 0;} ?>"></td>
</tr>
</table>
<p><br><textarea id="body" name="body" rows="30" cols="90"></textarea></p>
<p><input type="submit" name="send" value="Gui!"></p>
</form>
</fieldset>
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
