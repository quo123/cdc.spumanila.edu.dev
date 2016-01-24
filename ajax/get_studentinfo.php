<?php   
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

init_session() or die('Error: session has expired. Please log in again.');
init_my_cookie();
refresh_session() or die('Error: could not connect to server. Please log in again if the error persists.');
extend_timeout();

//print_r($_POST);

$db  =  new DBObject('cdc');

//$studno = $db->escape($_POST['studno']);
$lname = $db->escape(trim($_POST['lname']));
$fname = $db->escape(trim($_POST['fname']));
$mname = $db->escape(trim($_POST['mname']));
$course = $db->escape(trim($_POST['course']));
$year = intval($_POST['year']);
$address = $db->escape(trim($_POST['address']));
$Contact = $db->escape(trim($_POST['contact']));

$date = strtotime(trim($_POST['bday']));
$Bday = date('Y-m-d', $date);

$Age = intval($_POST['age']);
$Gender = $db->escape(trim($_POST['gender']));
$CivStat = $db->escape(trim($_POST['civStat']));
$Father = $db->escape(trim($_POST['father']));
$FatherPhone = $db->escape(trim($_POST['fatherPhone']));
$Mother = $db->escape(trim($_POST['mother']));
$MotherPhone = $db->escape(trim($_POST['motherPhone']));

$year = $db->escape($_POST['year']);

//$InCampusHours = intval($_POST['inCampusHours']);
//$OffCampusHours = intval($_POST['offCampusHours']);
$HasPhoto = isset($_POST['hasPhoto']) ? 1 : 0;
//$HasCert = isset($_POST['hasCert']) ? 1 : 0;
//$HasEvalForm = isset($_POST['hasEvalForm']) ? 1 : 0;

//$sql  = "INSERT INTO students VALUES('$lname', '$fname', '$mname', '$course', '$address', '$Contact', '$Bday', '$Age', '$Gender', '$CivStat', '$Father', '$FatherPhone', '$Mother', '$MotherPhone', '$NoOfSem', '$InCampusHours', '$OffCampusHours', '$HasPhoto', '$HasCert', '$HasEvalForm')";
$sql = <<<EOSQL
INSERT INTO
	students(
		lname, fname, mname,
		course, year, address,
		Contact, Bday, Age, Gender, CivStat, Father, FatherPhone, Mother, MotherPhone,
		HasPhoto
	)
	VALUES(
		'$lname', '$fname', '$mname',
		'$course', '$year', '$address',
		'$Contact', '$Bday', $Age, '$Gender', '$CivStat', '$Father', '$FatherPhone', '$Mother', '$MotherPhone',
		$HasPhoto
	)
EOSQL;
	
if ($db->query($sql)) {
//	$sid = $db->getLastID();
//	$tables = array('students');
//	$rows = array('sid');
//	$values = array($sid);
//	
//	$sql = "INSERT INTO offcampus (student) VALUES ($sid)";
//	$db->query($sql) or die(deleteLast($tables, $rows, $values, $db->getError()));
//	$offid = $db->getLastID();
//	$tables[] = 'offcampus';
//	$rows[] = 'offid';
//	$values[] = $offid;
//	
//	$sql = "INSERT INTO oncampus (student) VALUES ($sid)";
//	$db->query($sql) or die(deleteLast($tables, $rows, $values, $db->getError()));
//	$onid = $db->getLastID();
	
	echo "Record added!";
} else {
	die('There was an error running the query['.$db->getError().']');
}

function deleteLast($tables, $rows, $values, $error) {
	global $db;
	for ($i = 0; $i < count($tables); $i++) {
		$sql = "DELETE FROM $tables[$i] WHERE $rows[$i] = $values[$i]";
		$db->query($sql);
	}
	return $error;
}