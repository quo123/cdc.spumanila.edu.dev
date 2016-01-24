<?php
require_once 'classDB.php';

function init_my_cookie() {
	setcookie('__clog', session_id(), time()+60*60, '/');
}

function kill_my_cookie() {
	setcookie('__clog', session_id(), time()-3600, '/');
}

function init_session() {
	if (ini_set('session.use_cookies', '0') && ini_set('session.use_only_cookies', '0')) {
		if (isset($_COOKIE['__clog'])) {
			session_id($_COOKIE['__clog']);
			session_start();
			return true;
		} else {
			session_start();
			return false;
		}
	} else {
		return false;
	}
}

function extend_timeout() {
	$_SESSION['timeout'] = time()+60*30;
}


//refresh flags and reach
function refresh_session() {
	$db = new DBObject('cdc');
	$sql = "SELECT * FROM userinfo WHERE userid = {$_SESSION['userid']}";
	
	if (($result = $db->query($sql)) && (mysqli_num_rows($result) > 0)) {
		$row = mysqli_fetch_assoc($result);
		if ($row['status'] == 0) {
			return false;
		}
		$_SESSION['userid'] = $row['userid'];
		$_SESSION['username'] = $row['username'];
		$_SESSION['status'] = $row['status'];
		$_SESSION['admin'] = $row['admin'];
		return true;
	} else {
		echo $db->getError();
		return false;
	}
}

function getSettings() {
	$db = new DBObject('cdc');
	$sql = "SELECT schoolyear, semester FROM settings LIMIT 1";
	if ($res = $db->query($sql)) {
		$settings = mysqli_fetch_assoc($res);
		return array('year' => intval($settings['schoolyear']), 'sem' => intval($settings['semester']));
	} else {
		return false;
	}
}

/**
 * Use this if login is required to view page
 * @param string $to - set page to jump to after login
 */
function set_need_login($to = '/') {
	if (!isset($_COOKIE['__clog'])) {
		//redirect to "must log in" page
		header("location: $to");
		return false;
	} else {
		init_session();
		if (!isset($_SESSION['timeout']) or $_SESSION['timeout']-time() <= 0) {
			//redirect to "must log in" page
			header("location: $to");
			return false;
		} else {
			init_my_cookie();
			extend_timeout();
			return true;
		}
	}
}

function return_base() {
	return '<base href="http://cdc.spumanila.edu.dev/" />';
}

function echo_base() {
	echo return_base();
}

function url_return_space($url) {
	return rawurldecode(str_replace('_', '%20', $url));
}

function url_replace_space($url) {
	return str_replace(array('%2F', '%5C'), array('%252F', '%255C'), rawurlencode(str_replace(array(' ', '%20'), '_', $url)));
}

//replace html elements
function replace_html($dirtystring) {
	$ascii = array('&', '<', '>', '"', "'", '/', "\t", '+');
	$htmlc = array('&amp;','&lt;', '&gt;', '&quot;', '&#x27;', '&#x2F;', '&emsp;', '&plus;');
	
	return str_replace($ascii, $htmlc, $dirtystring);
}

/**
 * Full name creator for forum and news.
 * @param string $fname First Name, eg: John
 * @param string $lname Last Name, eg: Doe
 * @param string $mname Middle Name, eg: Dela Cruz
 * @return string Format: John D.C. Doe
 */
function create_name($fname, $lname, $mname = null, $order = 'standard') {
	$mi = '';
	if ($mname !== null) {
		$temp = explode(' ', $mname);
		foreach ($temp as &$i) {
			if (!empty($i)) {
				$i = strtoupper(substr($i, 0, 1)).'.';
			}
		}
		unset($i);
		$mi = implode(' ', $temp);
	}
//	first letter only according to http://www.census.gov.ph/civilregistration/problems-and-solutions/compound-middle-names-dela-cruz-quintos-deles-villa-roman
//	$mi = substr(strtoupper($mname), 0, 1).'.';
	
	if ($order === 'standard') {
		return trim(replace_html("$fname $mi $lname"));
	} else {
		return trim(replace_html("$lname, $fname $mi"));
	}
}

function hexcolor_to_rgb($hexcolor) {
	$hexcolor = str_replace('#', '', $hexcolor);
	if (strlen($hexcolor) === 6) {
		$rgb = str_split($hexcolor, 2);
	} else if (strlen($hexcolor) === 3) {
		$rgb = str_split($hexcolor, 1);
	} else {
		return false;
	}
	
	foreach ($rgb as &$i) {
		$i = hexdec($i);
	}
	unset($i);
	
	return $rgb;
}

function echo_common_forms() {
echo <<<EOHTML
	<div id="login-form" class="account-form" title="Login">
		<p>Please enter your login details.</p>
		<form>
			<input type="text" name="username" maxlength="20" placeholder="Username" class="focusglow" required="" autocomplete="off" />
			<input type="password" name="userpass" maxlength="20" placeholder="Password" class="focusglow" required="" />
			<input type="submit" tabindex="-1" value="Login">
		</form>
		<p class="response"></p>
	</div>
	
	<div id="register-form" class="account-form" title="Register">
		<p>Create a new account (subject to approval).</p>
		<form autocomplete="off">
			<input type="text" name="username" maxlength="20" placeholder="Username" class="focusglow" required="" />
			<input type="password" name="userpass" maxlength="20" placeholder="Password" class="focusglow" required="" />
			<input type="submit" tabindex="-1" value="Register">
		</form>
		<p class="response"></p>
	</div>
	
	<div id="settings-form" class="account-form student" title="School Year settings">
		<p>Set current school year and semester.</p>
		<form autocomplete="off">
			<label>Year:<input type="number" id="settings-year" name="settings-schoolyear" title="School Year" min="1900" max="2999" value="1900" class="focusglow" required="" style="width:80px"
				/><span class="label-span noselect"> &ndash; </span><span id="endyear" class="label-span noselect">0000</span></label><br />
			<label>Semester:<input type="number" id="settings-sem" name="settings-semester" title="Semester" min="1" max="2" value="0" class="focusglow" required="" style="width:60px" /></label>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
EOHTML;
}