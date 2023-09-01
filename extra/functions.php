
<?php
function secure_hash($password, $algo=PASSWORD_BCRYPT) {
	switch ($algo) {
	case PASSWORD_ARGON2ID:
		$options = ['memory_cost' => 262144, 'time_cost' => 1, 'threads' => 1];
	case PASSWORD_ARGON2I:
		$options = ['memory_cost' => 131072, 'time_cost' => 5, 'threads' => 1];
	default:
		$options = ['cost' => 12];

	$pwhash = password_hash($password, $algo, $options);
}

}
?>
