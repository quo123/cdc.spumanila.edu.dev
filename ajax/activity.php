<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php';

init_session() or die('Error: Session has expired. Please log in again.');
init_my_cookie();
refresh_session() or die('Error: Could not connect to server. Please log in again if the error persists.');
extend_timeout();

//print_r($_POST);

$db = new DBObject('cdc');
$sql = '1';
//die('sample');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['start'])) {
		$startstring = "{$_POST['start']} {$_POST['start-hour']}:{$_POST['start-minute']} {$_POST['start-ampm']}";
		$start = date_format(date_create_from_format('m/d/Y h:i a', $startstring), 'Y-m-d H:i:s');
		
		$endstring = "{$_POST['end']} {$_POST['end-hour']}:{$_POST['end-minute']} {$_POST['end-ampm']}";
		$end = date_format(date_create_from_format('m/d/Y h:i a', $endstring), 'Y-m-d H:i:s');
		
		$actid = intval($_POST['actid']);
		$type = $db->escape($_POST['type']);
		$sql = "INSERT INTO actdates(actid, type, start, end) VALUES($actid, '$type', '$start', '$end')";
//		echo $sql;
		if ($db->query($sql)) {
			echo "Record added!";
		} else {
			die('Error: '.$db->getError());
		}
		
	} else if (isset($_POST['delete'])) {
		$dateid = intval($_POST['delete']);
		$sql = "DELETE FROM actdates WHERE dateid = $dateid";
		echo $sql;
		if ($db->query($sql)) {
			echo "Record deleted!";
		} else {
			die('Error: '.$db->getError());
		}
		
	} else if (isset($_POST['edit'])) {
		$control = array('type' => '', 'actid' => '', 'data' => '');
		$numbers = array('student', 'schoolyear', 'semester', 'hours', 'eval', 'cert');
		$data;
		$loc;
		foreach ($_POST as $key => $value) {
			if (array_key_exists($key, $control)) {
				$control[$key] = in_array($key, $numbers) ? intval($value) : $value;
			} else {
				if ($key !== 'edit') {
					$key = str_replace('others-', '', $key);
					$loc = $db->escape($key);
					$value = $db->escape(trim($value));
					$data = in_array($key, $numbers) ? $key === 'hours' ? floatval($value) : intval($value) : "'$value'";
				}
			}
		}
		
		$tuple = strstr($control['type'], 'campus', true);
		$sql = "UPDATE {$control['type']} SET $loc = $data WHERE {$tuple}id = {$control['actid']}";
		if ($db->query($sql)) {
			echo "Record saved!";
		} else {
			die('Error: '.$db->getError());
		}
		
	} else if (isset($_POST['deleval'])) {
		$id = intval($_POST['deleval']);
		$sql = "DELETE FROM evaluation WHERE id = $id";
		if ($db->query($sql)) {
			echo 'Record deleted.';
		} else {
			die('Error: '.$db->getError());
		}
		
	} else if (isset($_POST['delact'])) {
		$actid = intval($_POST['delact']);
		$type = $db->escape($_POST['type']);
		$id = strstr($type, 'campus', true);
		$sql = "DELETE FROM $type WHERE {$id}id = $actid";
		
		if ($db->query($sql) && $db->getAffected() > 0) {
			echo "Record deleted!";
		} else {
			die('Error: Record does not exist.');
		}
	} else if (isset($_POST['delstud'])) {
		$student = intval($_POST['delstud']);
		$find = array('oncampus' => 'onid', 'offcampus' => 'offid');
		
		foreach ($find as $key => &$value) {
			$value = array($value);
			$sql = "SELECT $value[0] FROM $key WHERE student = $student";
			if (($result = $db->query($sql)) && ($db->getAffected() > 0)) {
				while ($row = mysqli_fetch_assoc($result)) {
					$value[] = $row[$value[0]];
				}
			}
		}
		unset($value);
		
		$success = true;
		
		foreach ($find as $type => $value) {
			foreach ($value as $actid) {
				if (ctype_digit($actid)) {
					$sql = "DELETE FROM actdates WHERE type = '$type' AND actid = $actid";
					if (!$db->query($sql)) {
						$success = false;
					}
				}
			}
			
			$sql = "DELETE FROM $type WHERE student = $student";
			if (!$db->query($sql)) {
				$success = false;
			}
		}
		
		$sql = "DELETE FROM evaluation WHERE student = $student";
		if (!$db->query($sql)) {
			$success = false;
		}
		
		$sql = "DELETE FROM students WHERE sid = $student";
		if (!$db->query($sql)) {
			$success = false;
		}
		
		if ($success) {
			echo "Record deleted!";
		}
		
	} else {
		$numbers = array('student', 'schoolyear', 'semester', 'hours', 'eval', 'cert');
		$others = array('category', 'initiator');
		$bools = array('eval', 'cert');
		$rows = array();
		$values = array();
		$table = '';
		foreach ($_POST as $key => $value) {
			if ($key === 'type') {
				$table = $db->escape($value);
			} else if (strstr($key, 'others-') !== false) {
				continue;
			} else {
				if ($key === 'semester') {
					($value > 0) or die('Error: Semester setting is corrupted.');
				}

				if (in_array($key, $others)) {
					$value = $value === 'others' ? $_POST['others-'.$key] : $value;
				}
				
				$value = in_array($key, $bools) ? 1 : $value;
				$value = $db->escape(trim($value));
				strlen($value) > 0 or die('Error: Invalid input.');
				$values[] = in_array($key, $numbers) ? $key === 'hours' ? floatval($value) : intval($value) : "'$value'";
//				$values[] = in_array($key, $numbers) ? $value : "'$value'";
				$rows[] = $db->escape($key);
			}
		}
		$rows = implode(', ', $rows);
		$values = implode(', ', $values);
		$sql = "INSERT INTO $table($rows) VALUES($values)";
		
		if ($db->query($sql)) {
			echo "Record added!";
		} else {
			die('Error: '.$db->getError());
		}
	}
	
	
}