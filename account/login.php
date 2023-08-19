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

	if (isset($_POST['login'])) {
		if (!empty($_POST['email'])) {
			$email = escape_data($_POST['email']);
		} else {
			$email = FALSE;
			$msg .= $loginphp['msg']['err_email'];
		}

		if (!empty($_POST['password'])) {
			$password = hash("sha384", escape_data($_POST['password']));

		} else {
			$password = FALSE;
			$msg .= $loginphp['msg']['err_password'];
		}

		if ($email && $password) {
			$query = "SELECT password, powerlevel FROM $table WHERE email=?";
			$result = mysqli_execute_query($dbc, $query, [$email]);
			$assoc = mysqli_fetch_assoc($result);
			if ($assoc) {
				if (password_verify($password, base64_decode($assoc['password']))) {
					$_SESSION['auth'] = $assoc['password'];
					header("Location: $protocol://$server/index.php");
					exit;
				} else {
					$msg .= $loginphp['msg']['err_wrong_auth'];
				}
			} else {
				$msg .= $loginphp['msg']['err_no_email'];
			}

			mysqli_free_result($result);
			mysqli_close($dbc);

		} else {
			$msg .= $loginphp['msg']['err_tryagain'];
		}
	}
?>
<?php
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
		<td style="width:30%;"><b><?php echo $loginphp['form_input']['email']; ?>:</b></td> <td><label for="email"><input id="email" type="text" name="email" size="32" maxlength="32" value="<?php if(isset($_POST['email'])) {echo stripslashes($_POST['email']);} ?>"></label></td>	
	<tr>
		<td style="width:10%;">&nbsp;</td>
		<td style="width:30%;"><b><?php echo $loginphp['form_input']['password']; ?>:</b></td> <td><label for="password"><input id="password" type="password" name="password" size="32" maxlength="64"></label></td>
	</tr>
	<tr>
		<td style="width:10%;">&nbsp;</td>
		<td style="width:30%;"><label for="login"><input id="login" type="submit" name="login" value="<?php echo $loginphp['form_input']['login']; ?>!"></label></td>
	</tr>
</table>
</fieldset>
</form>

<?php
	include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
?>
