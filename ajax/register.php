<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/classDB.php';

if (!empty($_POST['username']) && !empty($_POST['userpass'])) {
	$db = new DBObject('cdc');
	$username = $db->escape($_POST['username']);
	$hashpass = hash('md5', $_POST['userpass']);
	
	$sql = "INSERT INTO userinfo(username, userpass) VALUES('$username', '$hashpass')";
	
	sleep(1);
	if ($db->query($sql) && $db->getAffected() > 0) {
		echo 'Registration successful!';
	} else {
		die('Error: '.$db->getError());
	}
} else {
	die('Username/password field is empty.');
}