<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
$msg = NULL;
include_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
if (isset($_SESSION['auth'])) {
	header("Location: protocol://$server/index.php");
	exit();
}

if (!isset($_POST['login'])) {
	goto html;
}
if (!empty($_POST['email'])) {
	$email = $_POST['email'];
} else {
	$email = FALSE;
	$msg .= $loginphp['msg']['err_email'];
}

if (!empty($_POST['password'])) {
	$password = $_POST['password'];

} else {
	$password = FALSE;
	$msg .= $loginphp['msg']['err_password'];
}

if (! ($email && $password)) {
	$msg .= $loginphp['msg']['err_tryagain'];
	goto html;
}
$uinfo = get_user_info("WHERE email=?", "password, powerlevel", [$email]);
if (!$uinfo) {
	$msg .= $loginphp['msg']['err_no_email'];
	goto html;
}
if (!password_verify($password, $uinfo['password'])) {
	$msg .= $loginphp['msg']['err_wrong_auth'];
	goto html;
}
$_SESSION['auth'] = $uinfo['password'];
header("Location: $protocol://$server/index.php");
exit;
mysqli_free_result($result);
mysqli_close($dbc);
?>
<?php
	html:
        include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<h1><?=$loginphp['h1_title']; ?></h1>
<h2><?=$loginphp['h2_info']; ?></h2>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset>
<!-- <div align="center"> -->
<legend><b><?=$loginphp['cred_prompt']; ?></b></legend>
<table style="text-align: center; border-width:0; width:100%;">
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><b><?php echo $loginphp['form_input']['email']; ?></b>:</td>
	<td><input id="email" type="text" name="email" size="32" maxlength="127" value="<?php if(isset($_POST['email'])) {echo export_data($_POST['email']);} ?>"></td>
</tr>
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><b><?php echo $loginphp['form_input']['password']; ?>:</b></td> <td><input id="password" type="password" name="password" size="32" maxlength="64"></td>
</tr>
<tr>
	<td style="width:10%;">&nbsp;</td>
	<td style="width:30%;"><input id="login" type="submit" name="login" value="<?php echo $loginphp['form_input']['login']; ?>!"></td>
</tr>
</table>
</fieldset>
</form>
<?php
	include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
