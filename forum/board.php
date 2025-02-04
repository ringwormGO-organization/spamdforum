<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
require_once("inc/board_inc.php");
if (!isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/account/login.php");
	exit;
}
$msg = NULL;
$noform = FALSE;
if ($_SESSION['powerlevel'] < 0) {
	$noform = TRUE;
	$msg .= "Ban hien khong co quyen viet bai.\n";
	goto html;
}

$to_addr = $subject = $body = '';
$r_pwlvl = -1;
$w_pwlvl = 0;
$relate_to = 0;
$rid = &$relate_to;

if (!empty($_GET['editid'])) {
	$eid = intval($_GET['editid']);
	$emsg = get_msg_info("WHERE msg_id=?", "*", [$eid]);
	if (!$emsg) {
		$msg .= "Khong tim thay bai viet!\n";
		goto html;
	}
	$is_mod = FALSE;
	if ($emsg['from_addr'] != $_SESSION['email']) {
		if ($_SESSION['powerlevel'] < 50) {
			$msg .= "Bai viet nay khong phai cua ban!\n";
			goto html;
		}
		$is_mod = TRUE;
	}
	foreach ($emsg as $k => $value) {
		$$k = $value;
	}
}
if (isset($_GET['relate_to']))
	$relate_to = intval($_GET['relate_to']);
if (isset($_POST['relate_to']))
	$relate_to = intval($_POST['relate_to']);

if ($rid != 0) {
	$rmsg = get_msg_info("WHERE msg_id=?", "to_addr, r_pwlvl, "
			   . "w_pwlvl", [$rid]);
	if (!$rmsg) {
		$msg .= "Bai viet ban dang vao khong the duoc tim thay!";
		goto html;
	} else {
		if ($rmsg['w_pwlvl'] > $_SESSION['powerlevel']) {
			$msg .= "Ban khong co quyen dang vao bai viet nay.\n";
			goto html;
		} else {
			if (!isset($emsg)) {
				$to_addr = $rmsg['to_addr'];
				$r_pwlvl = $rmsg['r_pwlvl'];
				$w_pwlvl = $rmsg['w_pwlvl'];
			}
		}
	}
}

if (!isset($_POST['send'])) {
	goto html;
}

/* Spam fighting framework! */
$now = date("Y-m-d H:i:s");
$timecond = "last_edit > SUBDATE('$now', INTERVAL 1 HOUR)";
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
	 * User is restricted to 4 msg with the same relate_to
	 * and 40 msg per hour.
	 */
	$msg .= "Ban dang gui qua nhieu bai viet so voi muc quy dinh.\n";
	$noform = TRUE;
	/* Log or send mail code */
	goto html;
}
unset($limit_query);
mysqli_free_result($result);

$good_chars = "/^([\x20-\x7E\x{00C0}-\x{1EF9}]*)$/ium";
/* Alphanumeric plus Vietnamese characters plus some other character idk */
$ok = TRUE;
if (!empty($_POST['to'])) {
	$to_addr = $_POST['to'];
} else {
	$to_addr = !empty($_POST['to']) ? $_POST['to'] : NULL;
	$ok = FALSE;
	$msg .= "Nhap vao chu de thao luan hoac email nguoi dung!\n";
}
if (!empty($_POST['subject']) && strlen($_POST['subject']) < 255 &&
    preg_match($good_chars, $_POST['subject'])) {
	$subject = trim($_POST['subject']);
} else {
	$subject = !empty($_POST['subject']) ? $_POST['subject'] : NULL;
	$ok = FALSE;
	$msg .= "Chu de tin nhan khong hop le.\n";
}
if (!empty($_POST['body']) && mb_strlen($_POST['body']) < 65536 &&
    preg_match($good_chars, $_POST['body'])) {
	$body = $_POST['body'];
} else {
	$body = !empty($_POST['body']) ? $_POST['body'] : NULL;
	$ok = FALSE;
	$msg .= "Tin nhan khong hop le.\n";
}
if (isset($_POST['r_pwlvl'])) {
	if (($r_pwlvl = intval($_POST['r_pwlvl'])) > $_SESSION['powerlevel']) {
		/* Disallow creating msgs that the author can't even access */
		$r_pwlvl = $_SESSION['powerlevel'];
	}
}
if (isset($_POST['w_pwlvl'])) {
	if (($w_pwlvl = intval($_POST['w_pwlvl'])) > $_SESSION['powerlevel']) {
		/* As well as msgs that author can't write to. */
		$w_pwlvl = $_SESSION['powerlevel'];
	}
}
if ($ok == FALSE) {
	$msg .= "Hay thu lai.\n";
	goto html;
}
if (isset($emsg)) {
	$col = '';
	$changed = array();
	if ($emsg['r_pwlvl'] > $_SESSION['powerlevel']) {
		$msg .= "Ban khong duoc phep chinh sua bai viet nay.\n";
		goto html;
	}
	foreach ($emsg as $k => $value) {
		if ($$k != $value) {
			/* Only update what is changed */
			$col .= "$k=?,";
			$changed[] = $$k;
		}
	}
	if (empty($col)) {
		header("Location: $protocol://$server/forum/index.php?id=$eid");
		exit;
	}
	if ($is_mod && (strstr($col, "body") || strstr($col, "subject"))) {
		$msg .= "Quyen cao chuc trong cung khong duoc phep sua bai "
		    . "nguoi khac tuy tien!\n";
		goto html;
	}
	$col = substr($col, 0, -1);
	if (editmsg($dbc, $eid, $col, $changed)) {
		header("Location: $protocol://$server/forum/index.php?id=$eid");
		exit;
	} else {
		$msg .= "May chu hien dang gap truc trac. Xin loi vi "
		    . "su co nay.\n";
		goto html;
	}
}
$from_addr = $_SESSION['email'];
if (($id = newmsg($dbc, [$rid, $subject, $body, $from_addr, $to_addr,
			$r_pwlvl, $w_pwlvl])) != FALSE) {
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
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">$msg</p>";
}
if (!$noform) {
?>
<fieldset>
<legend><b>Viet tin nhan</b></legend>
<form name="newmsg" action="<?=$_SERVER['REQUEST_URI']; ?>" method="POST">
<table style="border-width:0; width:100%;">
<tr>
	<td><b>To:</b></td>
	<td><input type="text" name="to" size="64" maxlength="128"
	    value="<?=export_data($to_addr); ?>"></td>
</tr>
<tr>
	<td><b>Subject:</b></td>
	<td><input type="text" name="subject" size="64" maxlength="255"
	    value="<?=export_data($subject); ?>"></td>
</tr>
<tr>
	<td><b>R/W level:</b></td>
	<td><input type="text" name="r_pwlvl" size="3" maxlength="3"
	     value="<?=$r_pwlvl;?>">
	<input type="text" name="w_pwlvl" size="3" maxlength="3"
	 value="<?=$w_pwlvl;?>"></td>
</tr>
<tr>
	<td><b>Relate to:</b></td>
	<td><input type="text" name="relate_to" size="15" value="<?=$rid; ?>"></td>
</tr>
</table>
<p><br><textarea id="body" name="body" rows="30" cols="90">
<?=export_data($body); ?>
</textarea></p>
<p><input type="submit" name="send" value="Gui!"></p>
</form>
</fieldset>
<?php
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
