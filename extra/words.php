<?php
/* See file COPYING for permissions and conditions to use the file. */
if (!isset($_COOKIE['spamdforum_lang'])) {
	setcookie('spamdforum_lang', 'en', time()+2592000, '/', '', $config['secure'], false);
	$f_lang = 'en';
} else {
	$f_lang = $_COOKIE['spamdforum_lang'];
}
$common_fn = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang/common.json";
$fn = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang{$_SERVER['SCRIPT_NAME']}.json";
$eng = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/en{$_SERVER['SCRIPT_NAME']}.json";
/*
  * All json file open errors are ignored. Dealing with scripts like vote.php which
  * doesn't need a words file is more complex.
  */
$words = json_decode(@file_get_contents($fn), true);
/* merge with English words to display English when a translation is not available */
$words = array_merge(@json_decode(@file_get_contents($eng), true), $words);
$common = json_decode(file_get_contents($common_fn), true);
if ($_SERVER['SCRIPT_NAME'] == '/forum/index.php') {
	$view = "{$_SERVER['DOCUMENT_ROOT']}/extra/words/$f_lang/forum/view.php.json";
	$words = array_merge($words, json_decode(file_get_contents($view), true));
}
?>
