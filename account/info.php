<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
if (!isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/index.php");
	exit;
}
if (isset($_POST['update_info'])) {
	$msg = NULL;
	if (! (!empty($_POST['auth']) &&
	    password_verify($_POST['auth'], $_SESSION['auth']))) {
		$msg .= $words['msg']['err_auth'];
		goto stop;
	}
	if (!empty($_POST['name']) && $_POST['name'] != $_SESSION['name']) {
		if (!preg_match("/^([\x20-\x7E\x{00C0}-\x{1EF9}]*){4,64}$/iu",
		    $_POST['name'])) {
			$msg .= $words['msg']['err_name'];
			goto stop;
		}
		$name = $_POST['name'];
		$query = "UPDATE $table SET name=? WHERE password='$auth'";
		if (mysqli_execute_query($dbc, $query, [$name])) {
			$msg .= "Ten cua ban da duoc thay doi. \n";
		}
	}
	if (!empty($_POST['password']) &&
	    $_POST['password'] != $_SESSION['auth']) {
		if (! ($_POST['password'] == $_POST['verify'])) {
			$msg .= $words['msg']['err_password_mismatch'];
			goto stop;
		}
		if (!preg_match("/^[[:alnum:]$#@%^.]{14,64}$/",
		    $_POST['password'])) {
			$msg .= $words['msg']['err_password'];
		}
		$password = secure_hash($_POST['password'], PASSWORD_BCRYPT);
		$query = "UPDATE $table SET password=? WHERE password='$auth'";
		if (mysqli_execute_query($dbc, $query, [$password])) {
			$_SESSION['auth'] = $password;
			$msg .= "Mat khau cua ban da duoc thay doi. \n";
		}
	}
}
?>
<?php                                 
	stop:
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>
<h1><?=$words['h1_title']; ?></h1>
<p><a href="<?php echo "$protocol://$server/account/logout.php"; ?>">
<?=$words['logout'];?></a></p>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
?>
<?php
	/* Fetch current data */
	$query = "SELECT email, name FROM $table WHERE password=?";
	$uinfo = get_user_info("WHERE password=?", "*", [$auth]);
?>
<form name="infophp" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<fieldset>
<?php echo "<legend><a href=\"$protocol://$server/profiles.php?email={$_SESSION['email']}\">{$words['profile']}</a></legend>"; ?>
<table style="text-align: center; margin-left:auto; margin-right:auto; border-width:0; width:100%;">
<tr>
	<th style="width:30%;"><?=$words['form_input']['auth']; ?>:</th>
	<td><label for="password"><input id="auth" type="password" name="auth" size="64" maxlength="64"></label></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['email']; ?>:</th>
	<td><label for="email_readonly"><input id="email_readonly" type="text" name="email_readonly" size="64" maxlength="64" value="<?=export_data($uinfo['email']); ?>" readonly="readonly"></label></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['name']; ?>:</th>
	<td><label for="name"><input id="name" type="text" name="name" size="64" maxlength="64" value="<?=export_data($uinfo['name']); ?>"></label></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['password']; ?>:</th>
	<td><label for="password"><input id="password" type="password" name="password" size="64" maxlength="64"></label></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['verify']; ?>:</th>
	<td><label for="verify"><input id="verify" type="password" name="verify" size="64" maxlength="64"></label></td>
</tr>
<tr>
	<td style="width:30%;"><label for="register"><input id="register" type="submit" name="update_info" value="<?php echo $words['form_input']['update_info']; ?>!"></label></td>
</tr>
</table>
</fieldset>
</form>
<!-- KET THUC NOI DUNG TRANG -->
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
