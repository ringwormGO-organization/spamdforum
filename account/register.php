<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
function valid_name($name)
{
	$pattern = "/^([\x20-\x7E\x{00C0}-\x{1EF9}]*){4,127}$/iu";
	if (preg_match($pattern, $name)) {
		return $name;
	}
	return false;
}
function valid_email($email)
{
	if ((filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
	    FILTER_VALIDATE_EMAIL) && (strlen($email) < 127))) { 
		return $email;
	}

	return false;
}
function valid_passwd($passwd)
{
	if (preg_match("/^[[:alnum:]$#@%^.]{14,64}$/", $passwd)) {
		return $passwd;
	}
	return false;
}
?>
<?php
if (isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/index.php");
	exit();
}
if (!$registration_status) {
	goto html;
}

if (isset($_POST['register'])) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
	include_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
	$msg = NULL;
	$name = $email = $password = false;

	if (!($name = valid_name($_POST['name']))) {
		$msg .= $registerphp['msg']['err_name'];
	}

	if (!($email = valid_email($_POST['email']))) {
		$msg .= $registerphp['msg']['err_email'];
	}

	if ($_POST['password'] == $_POST['verify']) {
		if (!($passwd = valid_passwd($_POST['password']))) {
			$msg .= $registerphp['msg']['err_password'];
		}
	} else {
		$msg .= $registerphp['msg']['err_password_mismatch'];
	}

	if (! ($name && $email && $passwd)) {
		$msg .= $registerphp['msg']['err_tryagain'];
		goto html;
	}
	if (check_exist_email($email, $dbc)) {
		$msg .= $registerphp['msg']['err_email_existed'];
		goto html;
	}
	$password = secure_hash($passwd, PASSWORD_BCRYPT);
	$query = "INSERT INTO $table (name, email, password, powerlevel, reg_date, last_visit, last_ip) VALUES (?, ?, ?, ?, NOW(), NOW(), ?);";
	if (mysqli_execute_query($dbc, $query, [$name, $email, $password, 0, $_SERVER['REMOTE_ADDR']])) {
		$_SESSION['auth'] = $password;
		header("Location: $protocol://$server/index.php");
		exit();
	} else {
		$msg .= $registerphp['msg']['err_server'] . mysqli_error($dbc);
	}
	mysqli_close($dbc);
}
?>
<?php                                 
	html:
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>                                                                      
<h1><?=$registerphp['h1_title']; ?></h1>
<h2><?=$registerphp['h2_info']; ?></h2>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
if (!$registration_status) {
	echo "<h2>{$registerphp['reg_disabled']}</h2>";
} else {
?>
<form name="register" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<fieldset>
<legend><b>Nhap vao thong tin cua ban</b></legend>
<table style="text-align: center; margin-left:auto; margin-right:auto; 
              border-width:0; width:100%;">
<tr>
	<th style="width:30%;"><?php echo $registerphp['form_input']['name']; ?>:</th> 
	<td><input id="name" type="text" name="name" size="64" maxlength="127" value="<?php if(isset($_POST['name'])) {echo export_data($_POST['name']);} ?>"></td>
</tr>
<tr>	
	<th style="width:30%;"><?php echo $registerphp['form_input']['email']; ?>:</th> 
	<td><input id="email" type="text" name="email" size="64" maxlength="127" value="<?php if(isset($_POST['email'])) {echo export_data($_POST['email']);} ?>"></td>
</tr>
<tr>
	<th style="width:30%;"><?php echo $registerphp['form_input']['password']; ?>:</th> 
	<td><input id="password" type="password" name="password" size="64" maxlength="64"></td>
</tr>
<tr>
	<th style="width:30%;"><?php echo $registerphp['form_input']['verify']; ?>:</th> 
	<td><input id="verify" type="password" name="verify" size="64" maxlength="64"></td>
</tr>
<tr>
	<td style="width:30%;"><input id="register" type="submit" name="register" value="<?php echo $registerphp['form_input']['register']; ?>!"></td>
</tr>
</table>
</fieldset>

</form>
<!-- KET THUC NOI DUNG TRANG -->
<?php
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
