<?php
/* See file COPYING for permissions and conditions to use the file. */
ob_start();
if (!isset($_POST['submit']))
	goto html;
if (!empty($_POST['lang'])) {
	$langlist = ['en', 'vi'];
	if (in_array($_POST['lang'], $langlist)) {
		setcookie('spamdforum_lang', $_POST['lang'], time()+2592000,
			  '/', '', false, false);
		$_COOKIE['spamdforum_lang'] = $_POST['lang'];
	} else {
		setcookie('spamdforum_lang', 'vi', time()+2592000,
			  '/', '', false, false);
		$_COOKIE['spamdforum_lang'] = 'vi';
	}
}
?>
<?php
html:
$need_db = false;
require_once("{$_SERVER['DOCUMENT_ROOT']}/extra/config.php");
include("{$_SERVER['DOCUMENT_ROOT']}/html/header.html")
?>
<h1><?=$words['settings'];?></h1>
<p><a href="/account/info.php"><?=$words['link_account'];?></a></p>
<form name="lang" action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
<p><?=$words['languages'];?>: <select name="lang">
<option value=""><?=$f_lang;?></option>
<option value="en">English (Tiếng Anh)</option>
<option value="vi">Tiếng Việt (Vietnamese)</option>
</select></p>
<p><input type="submit" name="submit" value="ok"></p>
</form>
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
ob_end_flush();
?>
