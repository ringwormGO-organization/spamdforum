<?php
/* See file COPYING for permissions and conditions to use the file. */
$need_db = false;
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
$msg = NULL;
if (isset($_SESSION['auth'])) {
	header("Location: protocol://$server/index.php");
	exit();
}

if (!isset($_POST['login']))
	goto html;
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/dbconnect.php");
if ($_POST['email'])
	$email = $_POST['email'];
else {
	$email = FALSE;
	$msg .= $words['msg']['err_email'];
}

if ($_POST['password'])
	$password = $_POST['password'];
else {
	$password = FALSE;
	$msg .= $words['msg']['err_password'];
}

if (! ($email && $password)) {
	$msg .= $words['msg']['err_tryagain'];
	goto html;
}
$uinfo = get_user_info("WHERE email=?", "password, powerlevel", [$email]);
if (!$uinfo) {
	$msg .= $words['msg']['err_no_email'];
	goto html;
}
if (!password_verify($password, $uinfo['password'])) {
	$msg .= $words['msg']['err_wrong_auth'];
	goto html;
}
init_user_session($uinfo['password']);
header("Location: $protocol://$server/index.php");
exit;
?>
<?php
html:
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<h1><?=$words['h1_title']; ?></h1>
<h2><?=$words['h2_info']; ?></h2>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset>
<!-- <div align="center"> -->
<legend><b><?=$words['cred_prompt']; ?></b></legend>
<table style="text-align: center; border-width:0; width:100%;">
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><b><?php echo $words['form_input']['email']; ?></b>:</td>
	<td><input id="email" type="text" name="email" size="32" maxlength="127" value="<?php if(isset($_POST['email'])) {echo export_data($_POST['email']);} ?>"></td>
</tr>
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><b><?php echo $words['form_input']['password']; ?>:</b></td> <td><input id="password" type="password" name="password" size="32" maxlength="64"></td>
</tr>
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><input id="login" type="submit" name="login" value="<?php echo $words['form_input']['login']; ?>!"></td>
</tr>
</table>
</fieldset>
</form>
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
