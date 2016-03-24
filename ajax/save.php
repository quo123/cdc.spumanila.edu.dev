<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

init_session() or die('Error: session has expired. Please log in again.');
init_my_cookie();
refresh_session() or die('Error: could not connect to server. Please log in again if the error persists.');
extend_timeout();

print_r($_POST);
//die('sample');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$db = new DBObject(CURRENT_DB);
	$set = array();
	$id = '';
	$col = '';
//	(!empty($_POST['sid']) && ctype_digit($_POST['sid']))or die('Error: record does not exist.');
	$numbers = array('Age', 'offhours', 'onhours', 'HasPhoto', 'HasCert', 'HasEvalForm', 'schoolyear', 'semester');
	$bools = array('HasPhoto' => 'rp', 'HasCert' => 'rc', 'HasEvalForm' => 're');

	foreach ($_POST as $key => $value) {
		if ($key == 'sid' || $key == 'onid' || $key == 'offid') {
			ctype_digit($value) or die('Error: record does not exist.');
			$id = $value;
			$col = $db->escape($key);
		} else {
			$key = in_array($key, $bools) ? array_search($key, $bools) : $db->escape($key);
			$value = $key === 'Bday' ? date('Y-m-d', strtotime(trim($value))) : $db->escape($value);

			if (strstr($key, '-') === false) {
				$value = in_array($key, $numbers) ? $value : "'$value'";
				$sql = "UPDATE students SET $key = $value WHERE $col = $id";
			} else {
				$arrkey = explode('-', $key);
				$value = in_array($arrkey[1], $numbers) ? "$value" : "'$value'";
				if (empty($col)) {
					$sql = "UPDATE $arrkey[0] SET $arrkey[1] = $value";
				} else {
					$sql = "UPDATE $arrkey[0] SET $arrkey[1] = $value WHERE $col = $id";
				}
			}

			echo "$sql. ";
			if ($db->query($sql) && ($db->getAffected() > 0)) {
				echo "Saved.";
			} else {
				echo $db->getError();
			}
		}
	}
	
}