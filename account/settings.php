<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
	if (!isset($_SESSION['auth'])) {
		header("Location: $protocol://$server/index.php");
		exit;
	}
	if (isset($_POST['update_info'])) {
		require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
		include_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
		$msg = NULL;
		if (!empty($_POST['auth']) && password_verify(hash("sha384", escape_data($_POST['auth'])), base64_decode($_SESSION['auth']))) {
			if (!empty($_POST['name']) && $_POST['name'] != $_SESSION['name']) {
				if (preg_match("/^([\x20-\x7E\x{00C0}-\x{1EF9}]*){4,64}$/iu", $_POST['name'])) {
					$name = escape_data($_POST['name']);
					$query = "UPDATE $table SET name=? WHERE password='$auth'";
					if (mysqli_execute_query($dbc, $query, [$name])) {
						$msg .= "Ten cua ban da duoc thay doi. \n";
					}
				} else {
					$msg .= $settingsphp['msg']['err_name'];
				}
			}
			if (!empty($_POST['password']) && $_POST['password'] != $_SESSION['auth']) {
				if ($_POST['password'] == $_POST['verify']) {
					if (preg_match("/^[[:alnum:]$#@%^.]{14,64}$/", $_POST['password'])) {
						$password = base64_encode(password_hash(hash("sha384", escape_data($_POST['password'])), PASSWORD_ARGON2ID, ['memory_cost' => 262144, 'time_cost' => 6, 'threads' => 1]));
						$query = "UPDATE $table SET password=? WHERE password='$auth'";
						if (mysqli_execute_query($dbc, $query, [$password])) {
							$_SESSION['auth'] = $password;
							$msg .= "Mat khau cua ban da duoc thay doi. \n";
						}
					} else {
						$msg .= $settingsphp['msg']['err_password'];
					}
				} else {
					$msg .= $settingsphp['msg']['err_password_mismatch'];
				}
			}
		} else {
			$msg .= "Mat khau hien tai khong dung. \n";
		}
	}
?>

<?php                                 
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>                                                                      
<h1><?=$settingsphp['h1_title']; ?></h1>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
?>

<?php
	/* Fetch current data */
	$query = "SELECT email, name FROM $table WHERE password=?";
	/* Maybe, because $auth is safe: base64 encoded of password (hashed!!) and stored in $_SESSION */
	/* $query = "SELECT * FROM $table WHERE password=$auth"; */
	/* $userdata = mysqli_fetch_assoc(mysqli_query($query, $dbc)); */
	$userdata = mysqli_fetch_assoc(mysqli_execute_query($dbc, $query, [$auth]));
?>
<form name="register" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<fieldset>
<?php echo "<legend><a href=\"$protocol://$server/profiles.php?email={$_SESSION['email']}\">Ho so</a></legend>"; ?>
<table style="text-align: center; margin-left:auto; margin-right:auto; border-width:0; width:100%;">
	<tr>
		<th style="width:30%;"><?=$settingsphp['form_input']['auth']; ?>:</th>
		<td><label for="password"><input id="auth" type="password" name="auth" size="64" maxlength="64"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?=$settingsphp['form_input']['name']; ?>:</th>
		<td><label for="name"><input id="email_readonly" type="text" name="email_readonly" size="64" maxlength="64" value="<?=export_data($userdata['email']); ?>" readonly="readonly"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?=$settingsphp['form_input']['name']; ?>:</th> 
		<td><label for="name"><input id="name" type="text" name="name" size="64" maxlength="64" value="<?=export_data($userdata['name']); ?>"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?=$settingsphp['form_input']['password']; ?>:</th> 
		<td><label for="password"><input id="password" type="password" name="password" size="64" maxlength="64"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?=$settingsphp['form_input']['verify']; ?>:</th> 
		<td><label for="verify"><input id="verify" type="password" name="verify" size="64" maxlength="64"></label></td>
	</tr>
	<tr>
		<td style="width:30%;"><label for="register"><input id="register" type="submit" name="update_info" value="<?php echo $settingsphp['form_input']['update_info']; ?>!"></label></td>
	</tr>
</table>
</fieldset>

</form>
<!-- KET THUC NOI DUNG TRANG -->
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
