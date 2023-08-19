<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>posting board</title>
</head>
<body>
<?php
// striping all tags except allowed ones, addslashes if needed
// replace addslashes() with mysqli_real_escape_string when use with databases
function escape_data_in($data) {
	$data = addslashes(trim($data));
	return $data;
}

// just to strip backslashes
function escape_data_out($data) {
	$data = str_replace("&amp;", "&", htmlspecialchars(stripslashes(trim($data)), $flag = ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401));
	return $data;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$msg = NULL;
	if (!empty($_POST['title'])) {
		$title = escape_data_in($_POST['title']);
	} else {
		$msg .= "Title is required!\n";
	}

	if (!empty($_POST['content'])) {
		if (preg_match("/[\x20-\x7F\x{00C0}-\x{1EF9}]{1,24576}/iu", $_POST['content'])) {
			/* We may use \\0 or \\3, \\3 is for full domain name */
			$urlregex = "((http{1}s?):\/\/)" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]){2,4}" . "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.~?@\-\/]*)";
			/*
			http:// (one time) - hostname. (hostname "dot", 1+ time/char) - com (net, info, 4 characters) - :port (integer, 2-5 character, may exist) - path (not limited)
			*/
			$content = nl2br(preg_replace("/$urlregex/ium", '<a href="\\0">\\0</a>', escape_data_out($_POST['content'])));
		} else {
			$msg .= "Contain unacceptable characters.";
		}
	} else {
		$msg .= "Content is required!\n";
	}

	if ($title && $content) {
		echo "<h1>$title </h1><br />";
		echo $content;
	}

	if (isset($msg)) {
		$newmsg = nl2br($msg);
		echo "<p style=\"color:red\">$newmsg</p>";
	}
} else {
?>

<form name="testing form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset>
<legend><h3>Posting</h3></legend>
<input type="text" name="title" size="190" maxlength="255" placeholder="Title" />
<br /><br />
<textarea id="content" name="content" rows="30" cols="150" placeholder="Write a content...">
</textarea>
<br />
<input type="submit" name="submit" value="Post!" />
</fieldset>
</form>
<?php
}
?>
</body>
</html>
