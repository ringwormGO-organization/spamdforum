<?php
/* See file COPYING for permissions and conditions to use the file. */
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
if (isset($_SESSION['auth'])) {
	header("Location: $protocol://$server/settings.php");
	exit;
} else {
	header("Location: $protocol://$server/account/login.php");
	exit;
}
