<?php
$charset = 'utf8mb4';
$collation = 'utf8mb4_unicode_520_ci';
if (!isset($_POST['create'])) {
	goto html;
}
if (empty($_POST['dbname']))
	die("enter database name");
if (empty($_POST['mysql_user']))
	die("enter mysql user");
if (empty($_POST['mysql_password']))
	die("enter mysql password");
if (empty($_POST['mysql_host']))
	die("enter mysql host");
if (empty($_POST['admin_email']))
	die("enter admin email");
if (empty($_POST['admin_password']))
	die("enter admin password");
if (empty($_POST['engine']))
	die("select an engine");
DEFINE ('DB_ID', $_POST['mysql_user']);
DEFINE ('DB_PW', $_POST['mysql_password']);
DEFINE ('DB_HOST', $_POST['mysql_host']);
DEFINE ('DB_NAME', $_POST['dbname']);
$table = 'forum_user';
$msgtable = 'msgtable';

$dbc = mysqli_init();

if (!mysqli_real_connect($dbc, DB_HOST, DB_ID, DB_PW, DB_NAME)) {
	die("Khong the thiet lap ket noi den co so du lieu!");
}
mysqli_set_charset($dbc, "utf8mb4");

$userquery = "CREATE TABLE forum_user (
user_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(127),
email VARCHAR(127) NOT NULL,
password VARCHAR(255) NOT NULL,
powerlevel TINYINT NOT NULL DEFAULT 0,
reg_date DATETIME,
last_visit TIMESTAMP,
last_ip VARBINARY(16),
PRIMARY KEY (user_id),
UNIQUE KEY (email),
KEY (password),
KEY (powerlevel),
KEY (reg_date)
) ENGINE={$_POST['engine']} DEFAULT CHARSET=$charset COLLATE=$collation";

$msgquery = "CREATE TABLE forum_msg (
msg_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
relate_to INT UNSIGNED NOT NULL,
subject VARCHAR(255) NOT NULL,
body MEDIUMTEXT NOT NULL,
from_addr VARCHAR(127) NOT NULL,
to_addr VARCHAR(127) NOT NULL,
votes MEDIUMINT NOT NULL DEFAULT 0,
r_pwlvl TINYINT NOT NULL,
w_pwlvl TINYINT NOT NULL,
last_edit TIMESTAMP NOT NULL,
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (msg_id),
KEY (relate_to),
KEY (from_addr),
KEY (to_addr),
KEY (r_pwlvl),
KEY (w_pwlvl),
KEY (created_at)
) ENGINE={$_POST['engine']} DEFAULT CHARSET=$charset COLLATE=$collation";

$votequery = "CREATE TABLE forum_votes (
msg_id INT UNSIGNED NOT NULL,
author MEDIUMINT UNSIGNED NOT NULL,
amount TINYINT NOT NULL,
KEY (msg_id),
KEY (author),
KEY (amount)
) engine={$_POST['engine']} DEFAULT CHARSET=$charset COLLATE=$collation";

$adminaccount = "INSERT INTO $table (name, email, password, powerlevel, " .
    "reg_date, last_visit, last_ip) VALUES (?, ?, ?, ?, NOW(), " .
    "NOW(), ?)";
if (!mysqli_query($dbc, $userquery))
	die("error while creating user table: " . mysqli_error($dbc));
if (!mysqli_query($dbc, $msgquery))
	die("error while creating msg table: " . mysqli_error($dbc));
if (!mysqli_query($dbc, $votequery))
	die("error while creating vote table: " . mysqli_error($dbc));
$pwhash = password_hash($_POST['admin_password'], PASSWORD_BCRYPT,
    ['cost' => 13]);
$user_ip = inet_pton($_SERVER['REMOTE_ADDR']);
if (!mysqli_execute_query($dbc, $adminaccount, ["Admin", $_POST['admin_email'],
    $pwhash, 100, $user_ip]))
	die("error while creating admin user: " . mysqli_error($dbc));
die('the operation completed successfully, please delete this file and everything else on the install directory');
?>
<?php
html:
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Install tables to database</title>
	<link rel="stylesheet" type="text/css" href="/html/style.css">
</head>

<body>
<h2>Install tables to database</h2>
<p>This script will help you to install the forum_user and forum_msg table
to the database, and create an account that have administrative permission
on the forum.</p>
<p>Please enter the database name, the mysql user name and its password.
We assume that the mysql account have enough privileges to create tables
and indexes in the database, insert a row into the forum_user table.</p>
<form name="install_tables" action="<?=$_SERVER['PHP_SELF']; ?>" method="post">
<p><b>Database name:</b> <input type="text" name="dbname" size="40"></p>
<p><b>Mysql user name:</b> <input type="text" name="mysql_user" size="40"></p>
<p><b>Mysql password:</b>
    <input type="password" name="mysql_password" size="40"></p>
<p><b>Mysql host:</b>
    <input type="text" name="mysql_host" value="127.0.0.1" size="40"></p>
<p><b>Admin account email:</b>
    <input type="text" name="admin_email" size="40"></p>
<p><b>Admin password:</b>
    <input type="password" name="admin_password" size="40"></p>
<p>Table engine (default InnoDB)
<select name="engine">
<option value="InnoDB">InnoDB</option>
<option value="Aria">Aria (MariaDB only)</option>
<option value="MyISAM">MyISAM</option>
</select></p>
<p><input type="submit" name="create" value="create!"></p>
</form>

<hr>
</body>
</html>
