<?php
/* See file COPYING for permissions and conditions to use the file. */
ob_start();
$langlist = array('en' => 'English (Tiếng Anh)',
		  'vi' => 'Tiếng Việt (Vietnamese)'
		);
if (!isset($_POST['submit']))
	goto html;
if (!empty($_POST['lang'])) {
	if (array_key_exists($_POST['lang'], $langlist)) {
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
<?php
echo "<option value=\"$f_lang\">{$langlist[$f_lang]}</option>\n";
unset($langlist[$f_lang]);
foreach ($langlist as $lang => $name)
	echo "<option value=\"$lang\">$name</option>\n";
?>
</select></p>
<p><input type="submit" name="submit" value="ok"></p>
</form>
<?php
include("{$_SERVER['DOCUMENT_ROOT']}/html/footer.html");
ob_end_flush();
?>
