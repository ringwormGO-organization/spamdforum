<?php
/* See file COPYING for permissions and conditions to use the file. */
if (!isset($_COOKIE['spamdforum_lang'])) {
	setcookie('spamdforum_lang', 'vi', time()+2592000, '/', '', $secure,
		    false);
	$f_lang = 'vi';
} else {
	$f_lang = $_COOKIE['spamdforum_lang'];
}
$common_fn = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang/common.json";
$fn = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang{$_SERVER['SCRIPT_NAME']}.json";
$words = json_decode(file_get_contents($fn), true);
$common = json_decode(file_get_contents($common_fn), true);
if ($_SERVER['SCRIPT_NAME'] == '/forum/index.php') {
	$view = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang/forum/view.php.json";
	$words = array_merge($words, json_decode(file_get_contents($view), true));
}
?>
