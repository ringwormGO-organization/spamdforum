<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
if (!isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/account/login.php");
	exit;
}
$msg = NULL;
$noform = FALSE;
$to = '';
$rid = 0;
if (!empty($_GET['relate_to'])) {
	$rid = intval($_GET['relate_to']);
}
if (!empty($_POST['relate_id'])) {
	$rid = intval($_POST['relate_id']);
}
if (!isset($_POST['send'])) {
	goto html;
}

/* Spam fighting framework! */
$timecond = "last_edit > SUBDATE(CURRENT_TIMESTAMP, INTERVAL 1 HOUR)";
/* Count msg that have the current related_id the user sent in an hour */
$rq = "SELECT COUNT(*) FROM $msgtable WHERE from_addr='{$_SESSION['email']}' ".
      "AND relate_to=? AND $timecond";
/* Count msg that the user sent in an hour */
$aq = "SELECT COUNT(*) FROM $msgtable WHERE from_addr='{$_SESSION['email']}' ".
      "AND $timecond";
$limit_query = $rq . " UNION ALL " . $aq;
unset($rq);
unset($aq);
unset($timecond);
$result = mysqli_execute_query($dbc, $limit_query, [$rid]);
$msg_count = mysqli_fetch_all($result, MYSQLI_NUM);
if (intval($msg_count[0][0]) > 3 || intval($msg_count[1][0]) > 39) {
	/*
	 * User is restricted to 4 msg with the same relate_id
	 * and 40 msg per hour.
	 */
	$msg .= "Ban dang gui qua nhieu bai viet so voi muc quy dinh.\n";
	$noform = TRUE;
	/* Log or send mail code */
	goto html;
}
unset($limit_query);
mysqli_free_result($result);

if ($rid != 0) {
	$find_r = mysqli_execute_query($dbc, "SELECT w_pwlvl " .
		  "FROM $msgtable WHERE msg_id=$rid");
	if (mysqli_num_rows($find_r) != 1) {
		$msg .= "Khong tim thay bai viet ban dang de cap!\n";
		goto html;
	}
	$r_info = mysqli_fetch_assoc($find_r);
	if ($r_info['w_pwlvl'] > $_SESSION['powerlevel']) {
		$msg .= "Ban khong co quyen dang vao bai viet nay.\n";
		goto html;
	}
}

$good_chars = "/[\x20-\x7F\x{00C0}-\x{1EF9}]/iu";
/* Alphanumeric plus Vietnamese characters plus some other character idk */
if (!empty($_POST['to'])) {
	$to = $_POST['to'];
} else {
	$to = FALSE;
	$msg .= "Nhap vao chu de hoac email nguoi dung!\n";
}
if (!empty($_POST['subject']) && mb_strlen($_POST['subject']) < 255 &&
    preg_match($good_chars, $_POST['subject'])) {
	$subject = trim($_POST['subject']);
} else {
	$subject = FALSE;
	$msg .= "Chu de tin nhan khong hop le.\n";
}
if (!empty($_POST['body']) && mb_strlen($_POST['body']) < 65536 &&
    preg_match($good_chars, $_POST['body'])) {
	$body = $_POST['body'];
} else {
	$body = FALSE;
	$msg .= "Tin nhan khong hop le.\n";
}
/* "Muted" users can view any msg, but not write */
$r_pwlvl = -1;
$w_pwlvl = 0;
if (!empty($_POST['r_pwlvl'])) {
	if (($r_pwlvl = intval($_POST['r_pwlvl'])) > $_SESSION['powerlevel']) {
		/* Disallow creating msgs that the author can't even access */
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
$query = "INSERT INTO $msgtable (relate_to, subject, body, from_addr, to_addr, "
	. "r_pwlvl, w_pwlvl, last_edit) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
if (mysqli_execute_query($dbc, $query, [$rid, $subject, $body, $from, $to,
			 $r_pwlvl, $w_pwlvl])) {
	/* Redirect to the new msg if success */
	$id = mysqli_insert_id($dbc);
	header("Location: $protocol://$server/forum/index.php?id=$id");
	exit;
} else {
	$msg .= "May chu hien dang gap truc trac. Xin loi vi su co nay.\n";
}
?>
<?php
html:
$title = "Viet tin nhan";
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<?php
$rr_pwlvl = -1;
$rw_pwlvl = 0;
if ($rid) {
	$q = "SELECT to_addr, r_pwlvl, w_pwlvl FROM $msgtable WHERE msg_id=?";
	$r_result = mysqli_execute_query($dbc, $q, [$rid]);
	if (mysqli_num_rows($r_result) != 1) {
		$msg .= "Bai viet ban dang vao khong the duoc tim thay!";
	} else {
		$rmsg = mysqli_fetch_assoc($r_result);
		$to = export_data($rmsg['to_addr']);
		$rr_pwlvl = $rmsg['r_pwlvl'];
		$rw_pwlvl = $rmsg['w_pwlvl'];
	}
}
?>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">$msg</p>";
}
if (!$noform) {
?>
<fieldset>
<legend><b>Viet tin nhan</b></legend>
<form name="newmsg" action="<?=$_SERVER['PHP_SELF']; ?>" method="POST">
<table style="border-width:0; width:100%;">
<tr>
	<td><b>To:</b></td>
	<td><input type="text" name="to" size="64" maxlength="128"
	    value="<?=export_data($to); ?>"></td>
</tr>
<tr>
	<td><b>Subject:</b></td>
	<td><input type="text" name="subject" size="64" maxlength="255"
	    value="<?php if (!empty($_POST['subject']))
		   echo export_data($_POST['subject']); ?>"></td>
</tr>
<tr>
	<td><b>R/W level:</b></td>
	<td><input type="text" name="r_pwlvl" size="3" maxlength="3"
	     value="<?=$rr_pwlvl;?>">
	<input type="text" name="w_pwlvl" size="3" maxlength="3"
	 value="<?=$rw_pwlvl;?>"></td>
</tr>
<tr>
	<td><b>Relate to:</b></td>
	<td><input type="text" name="relate_id" size="15" value="<?=$rid; ?>"></td>
</tr>
</table>
<p><br><textarea id="body" name="body" rows="30" cols="90">
<?php
if (!empty($_POST['body']))
	echo export_data($_POST['body']);
?>
</textarea></p>
<p><input type="submit" name="send" value="Gui!"></p>
</form>
</fieldset>
<?php
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
