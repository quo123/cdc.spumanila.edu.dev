<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/classDB.php';
require_once 'includes/functions.php';

if (!empty($_POST['username']) && !empty($_POST['userpass'])) {
	$db = new DBObject('cdc');
	$username = $db->escape($_POST['username']);
	$hashpass = hash('md5', $_POST['userpass']);
	
	$sql = "SELECT * FROM userinfo WHERE userpass = '$hashpass' AND username = '$username'";
	
	$time = rand(1000*1000, 1000*500);
	usleep($time);
//	sleep(1);
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$row = mysqli_fetch_assoc($result);
		if ($row['status']) {
			init_session();
			init_my_cookie();
			$_SESSION['userid'] = $row['userid'];
			$_SESSION['username'] = $row['username'];
			$_SESSION['status'] = $row['status'];
			$_SESSION['admin'] = $row['admin'];
			extend_timeout();
			echo "Welcome back, {$row['username']}!";
		} else {
			echo "This account has not yet been activated.";
		}
	} else {
		die('Invalid username/password.');
	}
	
}