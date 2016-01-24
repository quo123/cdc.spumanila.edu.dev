<?php
/*****debug*****/
//echo '<html><head><title>Crypto</title></head><body><font face="Courier" size=2>';
////$start = microtime(true);
////$timestamp = get_timestamp();
////print_r($timestamp);
////echo encrypt_password("password", $timestamp[0]);
////$runtime = floor((microtime(true) - $start) * 1000);
////echo " runtime: {$runtime}us<br />";
////
////$timestamp = get_timestamp();
////$date = explode(" ", $timestamp[1]);
////print_r($timestamp);
////$newdate = date("F j, Y, H:i:s.", $date[1]).substr($timestamp[0], 10, 6);
////echo $newdate."<br />";
////$teststamp = create_timestamp($newdate);
////print_r($teststamp);
////echo create_microdate($teststamp);
//
//echo '</font></body></html>';

/*	TOP_CRYPT (C) TOP-OS, 2014
	Usage instructions (SQL)
	For registration:
		//assign registration timestamp to variable
		$timestamp = get_timestamp();
 * 
		//encrypt password using index 0 of timestamp array (combined seconds and microseconds)
		$encpass = encrypt_password($password, $timestamp[0]);
 * 
 *		//convert timestamp to readable date
 *		$regdate = create_microdate($timestamp);
 * 
		//insert data into database (regdate = registration date. Create new column)
		INSERT INTO [usertable] VALUES(null, $username, $encpass, $regdate...)
 * 
 * 
	For login verification:
		//look up username, select password and registration date. Salt is computed from regdate so we need that too
		SELECT [userpass], [regdate] FROM [usertable] WHERE [username] = '$username'
 * 
		//convert regtime to timestamp
		$timestamp = create_timestamp($row["regtime"]);
 * 
		//hash the enetered password
		$encpass = encrypt_password($password, $timestamp[0]);
 * 
		//compare to [userpass], if equal etc
*/

/**
 * Create a date string from a string generated by get_timestamp() or create_timestamp().
 * @param string $timestamp created by get_timestamp
 * @return string in format: August 27, 2014, 19:47:28.894163
 */
function create_microdate($timestamp) {
	date_default_timezone_set('Asia/Manila'); //put this at start of php code to sync time zone
	$date = explode(' ', $timestamp[1]);
	return date('F j, Y, H:i:s.', $date[1]).substr($timestamp[0], 10, 6); //returns date in format: August 27, 2014, 19:47:28.894163
}

/** @return array() in format: Array ( [0] => 1409140048894163 [1] => 0.89416300 1409140048 ) */
function get_timestamp() {
	date_default_timezone_set('Asia/Manila');
	$u = microtime();
	return array(substr($u, 11, 10).substr($u, 2, 6), $u); //returns array in format: Array ( [0] => 1409140048894163 [1] => 0.89416300 1409140048 )
}

/**
 * Create a timestamp with microsecond accuracy from a date string generated by create_microdate();
 * @param string $date in format: August 27, 2014, 19:47:28.894163
 * @return array() in format: Array ( [0] => 1409140048894163 [1] => 0.89416300 1409140048 )
 */
function create_timestamp($date) {
	date_default_timezone_set('Asia/Manila');
	$parts = explode('.', $date);
	return array(strtotime($parts[0]).$parts[1], '0.'."{$parts[1]}00".' '.strtotime($parts[0])); //same as above
}

/**
 * Generates a whirlpool hash of $pass using a salt derived from $regtime.
 * @param string $pass
 * @param string $regtime
 * @return string
 */
function encrypt_password($pass, $regtime) {
	$salt = '$2y$07$'.substr(md5($regtime), 8, 22);
	return hash('whirlpool', crypt($pass, $salt)); //returns 128-char length hex string, eg: 755abcc60c0c9ff9c7a5fc874ec26162bed26b4d3a9ad46295f58a354cff6536955a41b26b4ec1466b59490e41eb5a00282c612d1fb161bba98e49e628d28048
}