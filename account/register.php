<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
	if (isset($_SESSION['auth'])) {
		header("Location: $protocol://$server/index.php");
		exit();
	}
	if (isset($_POST['register'])) {
		require_once("{$_SERVER['DOCUMENT_ROOT']}/../dbconnect.php");
		include_once("{$_SERVER['DOCUMENT_ROOT']}/extra/words.php");
		$msg = NULL;
		if (preg_match("/^([\x20-\x7E\x{00C0}-\x{1EF9}]*){4,64}$/iu", $_POST['name'])) {
			$name = escape_data($_POST['name']);
		} else {
			$name = FALSE;
			$msg .= $registerphp['msg']['err_name'];
		}

		if (filter_var(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
			$email = escape_data($_POST['email']);
		} else {
			$email = FALSE;
			$msg .= $registerphp['msg']['err_email'];
		}

		if (preg_match("/^[[:alnum:]$#@%^.]{14,64}$/", $_POST['password'])) {
			if ($_POST['password'] == $_POST['verify']) {
				$password = TRUE;
			} else {
				$password = FALSE;
				$msg .= $registerphp['msg']['err_password_mismatch'];
			}
		} else {
			$password = FALSE;
			$msg .= $registerphp['msg']['err_password'];
		}

		if ($name && $email && $password) {
			$exist['email'] = check_email($email, $dbc);

			if ($exist['email'] == FALSE) {
				$password = base64_encode(password_hash(hash("sha384", escape_data($_POST['password'])), PASSWORD_ARGON2ID, ['memory_cost' => 262144, 'time_cost' => 6, 'threads' => 1]));
				$query = "INSERT INTO $table (name, email, password, powerlevel, reg_date, last_visit, last_ip) VALUES (?, ?, ?, ?, NOW(), NOW(), ?);";
				if (mysqli_execute_query($dbc, $query, [$name, $email, $password, 0, $_SERVER['REMOTE_ADDR']])) {
					$_SESSION['auth'] = $password;
					header("Location: $protocol://$server/index.php");
					exit();
				} else {
					$msg .= $registerphp['msg']['err_server'] . mysqli_error($dbc);
				}
			} else {
				if ($exist['email'] == TRUE) {
					$msg .= $registerphp['msg']['err_email_existed'];
				}
			}
			mysqli_close($dbc);

		} else {
			$msg .= $registerphp['msg']['err_tryagain'];
		}
	}
?>

<?php                                 
	include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html");
?>                                                                      
<h1><?=$registerphp['h1_title']; ?></h1>
<h2><?=$registerphp['h2_info']; ?></h2>
<?php
if (isset($msg)) {
	$msg = nl2br($msg);
	echo "<p style=\"color: red;\">{$msg}</p>";
}
?>

<form name="register" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<fieldset>
<legend><b>Nhap vao thong tin cua ban</b></legend>
<table style="text-align: center; margin-left:auto; margin-right:auto; border-width:0; width:100%;">
	<tr>
		<th style="width:30%;"><?php echo $registerphp['form_input']['name']; ?>:</th> 
		<td><label for="name"><input id="name" type="text" name="name" size="64" maxlength="64" value="<?php if(isset($_POST['name'])) {echo stripslashes($_POST['name']);} ?>"></label></td>
	</tr>
	<tr>	
		<th style="width:30%;"><?php echo $registerphp['form_input']['email']; ?>:</th> 
		<td><label for="email"><input id="email" type="text" name="email" size="64" maxlength="96" value="<?php if(isset($_POST['email'])) {echo stripslashes($_POST['email']);} ?>"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?php echo $registerphp['form_input']['password']; ?>:</th> 
		<td><label for="password"><input id="password" type="password" name="password" size="64" maxlength="64"></label></td>
	</tr>
	<tr>
		<th style="width:30%;"><?php echo $registerphp['form_input']['verify']; ?>:</th> 
		<td><label for="verify"><input id="verify" type="password" name="verify" size="64" maxlength="64"></label></td>
	</tr>
	<tr>
		<td style="width:30%;"><label for="register"><input id="register" type="submit" name="register" value="<?php echo $registerphp['form_input']['register']; ?>!"></label></td>
	</tr>
</table>
</fieldset>

</form>
<!-- KET THUC NOI DUNG TRANG -->
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
