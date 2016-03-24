<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

init_session() or die('Error: Session has expired. Please log in again.');
init_my_cookie();
refresh_session() or die('Error: Could not connect to server. Please log in again if the error persists.');
extend_timeout();

$db = new DBObject(CURRENT_DB);
$settings = getSettings();
$year = intval($settings['year']);
$sem = intval($settings['sem']);

if (isset($_GET['question'])) {
	$quest = intval($_GET['question']);
	$type = intval($_GET['type']);
	
	$questions = array(
		'What did you like most about the activity/event? Why?',
		'If you were to describe your experience using one word, it would be...',
		'What Paulinian Core Value was enfleshed/reflected/gained from the activity? Please cite some evidences to prove that such Paulinian Core Value was enfleshed/reflected/gained (ex. community - found new friends from other academic programs during the feeding).',
		'What virtue was inculcated in you after the activity?',
		'In a scale of 1 to 4, where 4 is the highest, please rate the activity. If less than 3, what aspects of the activity/event need to be improved?',
		'In a scale of 1 to 4, where 4 is the highest, please rate your participation. If less than 3, what do you intend to do to make your participation more active and meaningful?',
		'What insights/realizations did you gain from the activity/event?',
		'What is your next plan of action?'
		);
	
	$json = array();
	$ans = array();
	switch ($quest) {
		case 3: 
		case 5: 
		case 6: $ans[0] = "evaluation.q{$quest}e1 as ans1"; $ans[1] = "evaluation.q{$quest}e2 as ans2"; break;
		default: $ans[0] = "evaluation.q{$quest}e1 as ans1"; break;
	}
	
	$ans2 = join(', ', $ans);
	$question = $questions[$quest-1];
	$json['quest'] = $question;
	$where = $type ? '' : "AND schoolyear = $year AND semester = $sem";
	$sql = <<<EOSQL
SELECT evaluation.schoolyear, evaluation.semester, evaluation.student, $ans2, students.lname, students.fname, students.mname
FROM evaluation
INNER JOIN students ON evaluation.student = students.sid
WHERE reqcode = 'OK' $where ORDER BY schoolyear DESC, semester DESC, id DESC
EOSQL;
	
	if ($result = $db->query($sql)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$fullname = create_name($row['fname'], $row['lname'], $row['mname']);
			$answer2 = isset($row['ans2']) || !empty($row['ans2']) ? $row['ans2'] : null;
			
			$json['ans'][] = array(
				'answer' => $row['ans1'],
				'answer2' => $answer2,
				'sid' => $row['student'],
				'fullname' => $fullname,
				'year' => $row['schoolyear'],
				'sem' => $row['semester']
			);
		}
		echo json_encode($json);
	} else {
		die("Error: {$db->getError()} -- $sql");
	}
}