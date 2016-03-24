<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

$db = new DBObject(CURRENT_DB);

if (isset($_POST['evalcode'])) {
	if (strtoupper($_POST['evalcode']) === 'OK') {
		die('Error: Invalid evaluation code.');
	}
	$json = array();
	$reqcode = $db->escape(trim($_POST['evalcode']));
	$select = 'evaluation.id as evalid, evaluation.schoolyear, evaluation.semester, students.lname, students.fname, students.mname, students.course, students.year';
	$where = "evaluation.reqcode = '$reqcode'";
	$sql = "SELECT $select FROM evaluation INNER JOIN students ON evaluation.student = students.sid WHERE $where";
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$row = mysqli_fetch_assoc($result);
		$json['id'] = $row['evalid'];
		$json['name'] = create_name($row['fname'], $row['lname'], $row['mname'], 'reverse');
		$json['course'] = $row['course'];
		$json['year'] = $row['year'];
		$json['schoolyear'] = $row['schoolyear'];
		$json['semester'] = $row['semester'];
		echo json_encode($json);
	} else {
		die('Error: Invalid evaluation code.');
	}
} else if (isset($_POST['evaluation'])) {
	$set = array();
	$numbers = array('q5-1', 'q6-1');
	foreach($_POST as $key => $value) {
		if ($key !== 'evaluation') {
			$value = in_array($key, $numbers) ? intval($value) : "'".$db->escape($value)."'";
			$key = $db->escape(str_replace('-', 'e', $key));
			$set[] = "$key = $value";
		}
	}
	$eval = intval($_POST['evaluation']);
	$set = implode(', ', $set);
	
	$sql = "UPDATE evaluation SET $set, reqcode = 'OK' WHERE id = $eval";
	if ($db->query($sql)) {
		echo "Evaluation submitted.";
	} else {
		die('Error: An error was encountered while submitting the form.');
//		die('Error: '.$db->getError());
	}
	
}
