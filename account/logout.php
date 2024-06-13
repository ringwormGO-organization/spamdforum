<?php
/* See file COPYING for permissions and conditions to use the file. */
?>
<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
?>
<?php
	if (isset($_SESSION['auth'])) {
		$_SESSION = array();
		session_destroy();
		setcookie(session_name(), '', time()-1, '/', 0);
		header("Location: $protocol://$server/account/login.php");
                exit();
	} else {
		header("Location: $protocol://$server/index.php");
		exit();
	}
?>
