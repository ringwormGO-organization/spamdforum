<?php
/* See file COPYING for permissions and conditions to use the file. */
$need_db = false;
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
function newuser($dbc, $uinfo) {
	global $table;
	$now = date("Y-m-d H:i:s");
	$query = "INSERT INTO $table (name, email, password, powerlevel, "
		."reg_date, last_visit, last_ip) VALUES (?, ?, ?, ?, '$now', "
		."'$now', ?)";

	return mysqli_execute_query($dbc, $query, $uinfo);
}

function valid_name($name) {
	$pattern = "/^([\x20-\x7E\x{00C0}-\x{1EF9}]*){4,127}$/iu";
	if (preg_match($pattern, $name)) {
		return $name;
	}
	return false;
}
function valid_email($email) {
	if ((filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
	    FILTER_VALIDATE_EMAIL) && (strlen($email) < 127))) { 
		return $email;
	}

	return false;
}
function valid_passwd($passwd) {
	if (preg_match("/^[\x20-\x7E]{14,64}$/", $passwd)) {
		return $passwd;
	}
	return false;
}
if (isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/index.php");
	exit();
}

if (isset($_POST['register'])) {
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/dbconnect.php");
	if(!$config['registration_status'])
		goto html;
	$msg = NULL;
	$name = $email = $password = false;

	if (($name = valid_name($_POST['name'])) == false) {
		$msg .= $words['msg']['err_name'];
	}

	if (($email = valid_email($_POST['email'])) == false) {
		$msg .= $words['msg']['err_email'];
	}

	if ($_POST['password'] == $_POST['verify']) {
		if (($passwd = valid_passwd($_POST['password'])) == false) {
			$passwd = FALSE;
			$msg .= $words['msg']['err_password'];
		}
	} else {
		$passwd = FALSE;
		$msg .= $words['msg']['err_password_mismatch'];
	}

	if (! ($name && $email && $passwd)) {
		$msg .= $words['msg']['err_tryagain'];
		goto html;
	}
	if (email_exists($email, $dbc)) {
		$msg .= $words['msg']['err_email_existed'];
		goto html;
	}
	$password = secure_hash($passwd, PASSWORD_BCRYPT);
	$user_ip = inet_pton($_SERVER['REMOTE_ADDR']);
	if (newuser($dbc, [$name, $email, $password, 0, $user_ip])) {
		init_user_session($password);
		header("Location: $protocol://$server/index.php");
		exit();
	} else {
		$msg .= $words['msg']['err_server'] . mysqli_error($dbc);
	}
}
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
if (!$config['registration_status']) {
	echo "<h2>{$words['reg_disabled']}</h2>";
} else {
?>
<form name="register" action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
<fieldset>
<legend><b><?=$words['form_input']['legend'];?></b></legend>
<table style="text-align: center; margin-left:auto; margin-right:auto;
border-width:0; width:100%;">
<tr>
	<th style="width:30%;"><?=$words['form_input']['name'];?>:</th>
	<td><input id="name" type="text" name="name" size="64" maxlength="127" value="<?php if(isset($_POST['name'])) {echo export_data($_POST['name']);} ?>"></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['email'];?>:</th>
	<td><input id="email" type="text" name="email" size="64" maxlength="127" value="<?php if(isset($_POST['email'])) {echo export_data($_POST['email']);} ?>"></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['password'];?>:</th>
	<td><input id="password" type="password" name="password" size="64" maxlength="64"></td>
</tr>
<tr>
	<th style="width:30%;"><?=$words['form_input']['verify'];?>:</th>
	<td><input id="verify" type="password" name="verify" size="64" maxlength="64"></td>
</tr>
<tr>
	<td style="width:30%;"><input id="register" type="submit" name="register" value="<?=$words['form_input']['register'];?>"></td>
</tr>
</table>
</fieldset>

</form>
<!-- KET THUC NOI DUNG TRANG -->
<?php
}
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
