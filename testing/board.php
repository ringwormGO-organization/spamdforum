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
	$data = str_replace("&amp;", "&", addslashes(htmlspecialchars(trim($data))));
	return $data;
}

// just to strip backslashes
function escape_data_out($data) {
	$data = stripslashes(trim($data));
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
		if (preg_match("/^[[:alnum:]\x{00C0}-\x{1EF9}' -_]{64,24576}$/ius/", $_POST['content'])) {
			$content = nl2br(preg_replace("/((http{1}s?):\/\/)([[:alnum:]-])(\.)+([[:alnum:]-]){2,4}([[:alnum:]/+=\%&_.~?-]*)/ius", '<a href="\\0">\\0</a>', escape_data_in($_POST['content'])));
		} else {
			$msg .= "Contain unacceptable characters."
		}
	} else {
		$msg .= "Content is required!\n";
	}

	if ($title && $content) {
		echo "<h1>$title </h1><br />";
		echo escape_data_out($content);
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
