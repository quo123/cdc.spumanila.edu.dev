<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

init_session() or die('Error: Session has expired. Please log in again.');
init_my_cookie();
refresh_session() or die('Error: Could not connect to server. Please log in again if the error persists.');
extend_timeout();

$db = new DBObject('cdc');
$settings = getSettings();
$year = intval($settings['year']);
$sem = intval($settings['sem']);

if (isset($_GET['term'])) {
	$sql = standard_search($db, $_GET['term'], 'sid, lname, fname, mname');
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$fullname = create_name($row['fname'], $row['lname'], $row['mname'], 'lname');
			$json[] = array(
				'id' => $row['sid'],
				'label' => $fullname,
				'value' => $fullname
			);
		}
		
		echo json_encode($json);
	}
	
} else if (isset($_GET['student'])) {
	$sid = $db->escape($_GET['student']);
	$fields = 'sid, fname, lname, mname, course, HasPhoto';
	$tables = 'students';
	$orderby = 'ORDER BY sid DESC';
	
	if ($sid === 'ALL') {
		echo "<legend>All students</legend>\n";
		$sql = "SELECT $fields FROM students $orderby";
	} else if ($sid === 'RECENT') {
		echo "<legend>Recently added students</legend>\n";
		$sql = "SELECT $fields FROM students $orderby LIMIT 20";
	} else {
		echo "<legend>Search results</legend>\n";
		if (($sid = intval($sid)) > 0) {
			$sql = "SELECT $fields FROM students WHERE sid = $sid $orderby";
		} else {
			$sql = standard_search($db, $_GET['student'], $fields);
		}
	}
	
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$html = '';
		while ($row = mysqli_fetch_assoc($result)) {
			$fullname = '';
			$incampus = '';
			$offcampus = '';
			$photo = '';
			$cert = '';
			$eval = '';
			
			$ontotal = 0;
			
			$fullname = create_name($row['fname'], $row['lname'], $row['mname'], 'lname');
			$photo = good_or_bad('Photo', $row['HasPhoto'], 1, 'rp');

			$ondetails = getReqDetails('oncampus', $row['sid'], $year, $sem, false);
			$incampus = good_or_bad('On-campus hours', $ondetails['total'], 8, 'ri');
//			$ineval = good_or_bad('Evaluation', $ondetails['eval'], 1, 're');
			
			$offdetails = getReqDetails('offcampus', $row['sid'], $year, $sem, true);
			$offcampus = good_or_bad('Off-campus hours', $offdetails['total'], 4, 'ro');
//			$offeval = good_or_bad('Evaluation', $offdetails['eval'], 1, 're');
			$cert = good_or_bad('Certificate', $offdetails['cert'], 1, 'rc');
			$haseval = getEvalDetails($row['sid'], $year, $sem);
			$eval = good_or_bad('Evaluation', $haseval, 1, 're');
			
$html .= <<<EOHTML
<div class="resultbox noselect">
	<span class="result-name">$fullname</span><span class="result-edit quickbutton" data="{$row['sid']}">edit</span><br />
	<span class="result-course">{$row['course']}</span><span class="result-details quickbutton" Title="Details of Community Service">details</span><br />
	<br />
	<ul class="result-reqs">
		$photo$eval
	</ul><br />
	<ul class="result-reqs">
		$incampus
	</ul><br />
	<ul class="result-reqs">
		$offcampus$cert
	</ul>	
</div>
EOHTML;
		}
		echo $html;
	} else {
		die('No matching records found.');
	}
	
} else if (isset($_GET['allstudents'])) {
	$sql = "SELECT sid, lname, fname, mname, course FROM students ORDER BY lname ASC, fname ASC";
	$students = array();
	if ($result = $db->query($sql)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$students[] = $row;
		}
		echo json_encode($students);
	}
	
} else if (isset($_GET['edit'])) {
	$sid = intval($_GET['edit']);
$fields = <<<EOFIELDS
sid, fname, lname, mname, course, year, address,
Contact, Bday, Age, Gender, CivStat, Father, FatherPhone, Mother, MotherPhone
EOFIELDS;
	$sql = "SELECT $fields FROM students WHERE sid = $sid";
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$date = strtotime(trim($row['Bday']));
			$row['Bday'] = date('m/d/Y', $date);
			echo json_encode($row);
		}
	}
	
} else if (isset($_GET['editsettings'])) {
	echo json_encode(getSettings());
	
} else if (isset($_GET['acts']) && isset($_GET['sid'])) {
	$table = $db->escape($_GET['acts']);
	$sid = intval($_GET['sid']);
	$sql = "SELECT * FROM $table WHERE student = $sid ORDER BY schoolyear DESC, semester DESC";
	$acts = array();
	if (($result = $db->query($sql)) && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$acts[] = $row;
		}
	}	
	echo json_encode($acts);
	
} else if (isset($_GET['editact']) && isset($_GET['type'])) {
	$actid = intval($_GET['editact']);
	$type = $db->escape($_GET['type']);
	$field = $db->escape($_GET['field']);
	$tuple = strstr($type, 'campus', true);
	$sql = "SELECT $field FROM $type WHERE {$tuple}id = '$actid'";
	if (($result = $db->query($sql)) && mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_assoc($result);
		echo $row[$field];
	}
	
} else if (isset($_GET['getsched'])) {
//	print_r($_GET);
	$actid = intval($_GET['actid']);
	$type = $db->escape($_GET['type']);
	$sql = "SELECT dateid, start, end FROM actdates WHERE actid = $actid AND type = '$type' ORDER BY dateid DESC";
	
	$scheds = array();
	if (($result = $db->query($sql)) && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			foreach ($row as $key => &$value) {
				if ($key !== 'dateid') {
					$value = date_format(date_create_from_format('Y-m-d H:i:s', $value), 'M d, Y, h:i A');
				}
			}
			$scheds[] = $row;
		}
	}
	echo json_encode($scheds);
} else if (isset($_GET['evalcode'])) {
	$sid = intval($_GET['evalcode']);
	$settings = getSettings();
		
	$sql = "SELECT reqcode FROM evaluation WHERE student = $sid AND schoolyear = {$settings['year']} AND semester = {$settings['sem']}";
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$row = mysqli_fetch_array($result);
		echo $row['reqcode'];
	} else {
		echo 'N/A';
	}
} else if (isset($_GET['completereqs'])) {
	$sid = intval($_GET['completereqs']);
	$settings = getSettings();
	
	$ondetails = getReqDetails('oncampus', $sid, $year, $sem, false);
	$incampus = good_or_bad('On-campus hours', $ondetails['total'], 8, 'ri', false);

	$offdetails = getReqDetails('offcampus', $sid, $year, $sem, true);
	$offcampus = good_or_bad('Off-campus hours', $offdetails['total'], 4, 'ro', false);
	
	$cert = good_or_bad('Certificate', $offdetails['cert'], 1, 'rc', false);
	
	$lack[] = $incampus ? null : 'on-campus hours';
	$lack[] = $offcampus ? null : 'off-campus hours';
	$lack[] = $cert ? null : 'certificate(s)';
	$lack = array_filter($lack);
	$end = count($lack) > 1 ? ' and '.array_pop($lack) : '';
	$lack = implode(', ', $lack);
	$lacking = ($incampus && $offcampus && $cert) ? '' : "Lacks $lack$end.";
	
	($incampus && $offcampus && $cert) or die("Error: Requirements not fulfilled. $lacking");
} else if (isset($_GET['genreq'])) {
	$code = generateCode();
	$exists = true;
	$sid = intval($_GET['genreq']);
	$settings = getSettings();
	
	while ($exists) {
		$sql = "SELECT COUNT(id) as num_id FROM evaluation WHERE reqcode = '$code'";
		if ($result = $db->query($sql)) {
			$row = mysqli_fetch_array($result);
			if ($row['num_id'] == 0) {
				$exists = false;
			}
		} else {
			$exists = false;
		}
	}
	
	if (!$exists) {
		$sql = "SELECT id FROM evaluation WHERE student = $sid AND schoolyear = {$settings['year']} AND semester = {$settings['sem']}";
		
		if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
			$row = mysqli_fetch_assoc($result);
			$sql = "UPDATE evaluation SET reqcode = '$code' WHERE id = {$row['id']}";
		} else {
			$sql = "INSERT INTO evaluation(reqcode, student, schoolyear, semester) VALUES('$code', $sid, {$settings['year']}, {$settings['sem']})";
		}
		
		if ($db->query($sql) && $db->getAffected() > 0) {
			echo $code;
		} else {
			die('Error: '.$db->getError());
		}
	}
} else if (isset($_GET['vieweval'])) {
	$student = intval($_GET['vieweval']);
	$sql = "SELECT id, schoolyear, semester FROM evaluation WHERE student = $student AND reqcode = 'OK' ORDER BY schoolyear DESC, semester DESC";
	
	$evals = array();
	if (($result = $db->query($sql)) && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			foreach ($row as $key => &$value) {
				if ($key === 'schoolyear') {
					$value = $value.'&ndash;'.$value+1;
				}
			}
			$evals[] = $row;
		}
	}
	echo json_encode($evals);
} else if (isset($_GET['viewevalform'])) {
	$id = intval($_GET['viewevalform']);
	$select = 'schoolyear, semester, q1e1, q2e1, q3e1, q3e2, q4e1, q5e1, q5e2, q6e1, q6e2, q7e1, q8e1';
	$sql = "SELECT $select FROM evaluation WHERE id = $id";
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$row = mysqli_fetch_assoc($result);
		foreach ($row as $key => $value) {
			$newkey = preg_replace_callback('/q\d(e)\d/', function($matches) {
				return str_replace($matches[1], '-', $matches[0]);
			}, $key);
			
			if ($key !== $newkey) {
				$row[$newkey] = $value;
				unset($row[$key]);
			}
		}
		echo json_encode($row);
	} else {
		die('Error: '.$db->getError());
	}
}

function generateCode() {
	$num = str_split('0123456789');
	$let = str_split('QWERTYUIOPASDFGHJKLZXCVBNM');
	$form = str_split('00-000-000');
	foreach($form as &$i) {
		if ($i !== '-') {
			if (rand(0, 10) > 7) {
				$i = $num[rand(0, count($num)-1)];
			} else {
				$i = $let[rand(0, count($let)-1)];
			}
		}
	}
	return implode('', $form);
}

function getEvalDetails($sid, $year, $sem) {
	global $db;
	$sql = "SELECT COUNT(id) as haseval FROM evaluation WHERE student = $sid AND schoolyear = $year AND semester = $sem AND reqcode = 'OK'";
	if ($result = $db->query($sql)) {
		$row = mysqli_fetch_array($result);
		return $row['haseval'];
	} else {
		return false;
	}
}

function getReqDetails($table, $sid, $year, $sem, $cert) {
	global $db;
	$details = array('total' => 0, 'cert' => 0);
	$fields = 'hours';
	$certs = array();
	
	$fields .= $cert ? ', cert' : '';
	
	$sql = "SELECT $fields FROM $table WHERE student = $sid AND schoolyear = $year AND semester = $sem";
	if (($res = $db->query($sql))) {
		while ($row = mysqli_fetch_array($res)) {
			$details['total'] += $row['hours'];
			if ($cert) {$certs[] = $row['cert'];}
		}
		$details['cert'] = empty($certs) || in_array(0, $certs) ? 0 : 1;
	}
	return $details;
}

function standard_search($db, $get, $reqs) {
	$search = trim($get);
	$terms = explode(' ', $search);
	foreach ($terms as &$i) {
		if (strlen($i) > 0) {
			$i = "$i*";
		}
	}
	$search = implode(' ', $terms);
	
	$matchwhere = "MATCH(lname, fname) AGAINST('{$db->escape($search)}' IN BOOLEAN MODE)";
	$matchselect = "$matchwhere as relevance";
	$orderby = 'ORDER BY relevance DESC, lname ASC, fname ASC';
	
	return "SELECT $reqs, $matchselect FROM students WHERE $matchwhere $orderby";
}

function good_or_bad($title, $data, $threshold, $id, $text = true) {
	$good = $data < $threshold ? 'bad' : 'good';
	if (strstr($title, 'hours') == FALSE) {
		$stitle = strtolower($title);
		$title = $data < $threshold ? "Lacks $stitle" : "Complete $stitle";
		$data = strtoupper(substr($stitle, 0, 1));
	} else {
		$data = number_format($data, 1);
	}
$out = <<<OUT
<li class="reqs $good" title="$title" data-id="$id">$data</li>
OUT;
	return $text ? $out : ($good == 'good' ? true : false);
}