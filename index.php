<?php
date_default_timezone_set('Asia/Manila');
require_once 'includes/functions.php'; //classDB included
$cooking = init_session();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<base href="http://cdc.spumanila.edu.dev" />
	<title>Community Development Center</title>
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/cdc.css">
	<script src="script/jquery-1.11.1.min.js"></script>
	<script src="script/jquery-ui.min.js"></script>
	<script src="script/jquery.tablesorter.min.js"></script>
	<!--<script src="script/prescript.js"></script>-->
	<script src="script/prescript.min.js"></script>
</head>

<body class="cdc">
	<header class="noselect">
		<nav>
			<div class="constrain">
				<ul id="top-nav">
					<li class="current-nav"><a href="http://cdc.spumanila.edu.dev"><span>Faculty</span></a>
					</li><li class="other-nav"><a href="http://cdc.spumanila.edu.dev/student/"><span>Student</span></a></li>
				</ul>
				<ul id="login">
					<?php if ($cooking) {?>
					<li class="controls"><a href="javascript:;" id="settings-button"><span>Settings</span></a>
					</li><li class="controls"><a href="javascript:;" id="logout-button"><span>Logout</span></a></li>
					<?php } else { ?>
					<li class="controls"><a href="javascript:;" id="login-button"><span>Login</span></a>
					</li><li class="controls"><a href="javascript:;" id="register-button"><span>Register</span></a></li>
					<?php } ?>
				</ul>
			</div>
		</nav>
	</header>
	
	<div id="body">
		<div id="container" class="constrain<?php echo $cooking ? '' : ' splash'; ?>">
			<?php if ($cooking) {?>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Records</a></li>
					<li><a href="#tabs-2">Reports</a></li>
				</ul>
				<div id="tabs-1">
					<button id="add-student" tabindex="1">+ Add new student</button>
					<form id="searchbox">
						<input type="search" id="searchfield" name="search" placeholder="Search" tabindex="2"
						/><a id="searchbutton" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-search"></span></a>
					</form>
					<fieldset id="search-results">
						<legend>Recently added students</legend>
					</fieldset>
				</div>
				<div id="tabs-2">
					<select id="report-questions">
						<option selected disabled>Select Item</option>
						<?php 
							for ($i = 1; $i <= 8; $i++) {
								echo "<option value=\"$i\">Item #$i</option>";
							}
						?>
					</select>
					<select id="report-mod">
						<option selected value="0">Current</option>
						<option value="1">All</option>
					</select>
					<p id="quest">&nbsp;</p>
					<fieldset id="report-results">
						<legend>Answers</legend>
					</fieldset>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	
	<div id="footer">
		
	</div>
	
	<?php echo_common_forms();	?>
	
	<div id="add-form" class="account-form student" title="Add new student">
		<p class="response">Input student information.</p>
		<form autocomplete="off">
			<input type="text" name="lname" maxlength="25" title="Last Name" placeholder="Last Name" class="focusglow" required="" style="width:280px"
			/><input type="text" name="fname" maxlength="25" title="First Name" placeholder="First Name" class="focusglow" required="" style="width:280px"
			/><input type="text" name="mname" maxlength="4" title="Middle Initial(s)" placeholder="M.I." class="focusglow" required="" style="width:60px"
			/><input type="text" name="course" maxlength="10" title="Course" placeholder="Course" class="focusglow" required="" style="width:150px"
			/><label>Year:<input type="number" name="year"  title="Year Level" min="1" max="5" value="1" step="1" class="focusglow" required="" style="width:40px" /></label>
			<input type="text" name="address" maxlength="90" title="Home Address" placeholder="Home Address" class="focusglow" required="" style="width:680px"
			/><input type="tel" name="contact" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/><input type="text" id="bday" name="bday" maxlength="10" title="Birthdate" placeholder="Birthdate" class="focusglow" required="" style="width:204px"
			/><input type="text" id="age" name="age" maxlength="2" title="Age" placeholder="Age" readonly="" style="width:40px"
			/><select name="gender" title="Gender" class="focusglow" required="">
				<option value="" disabled selected>Gender</option>
				<option value="m">Male</option>
				<option value="f">Female</option>
			</select><select name="civStat" title="Civil Status" class="focusglow" required="">
				<option value="" disabled selected>Civil Status</option>
				<option value="s">Single</option>
				<option value="m">Married</option>
				<option value="d">Divorced</option>
				<option value="p">Separated</option>
				<option value="w">Widowed</option>
			</select>
			<br />
			<br />
			<input type="text" name="father" maxlength="64" title="Father's Name" placeholder="Father's Name" class="focusglow" required="" style="width:680px"
			/><input type="tel" name="fatherPhone" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/><input type="text" name="mother" maxlength="64" title="Mother's Name" placeholder="Mother's Name" class="focusglow" required="" style="width:680px"
			/><input type="tel" name="motherPhone" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/>
			<br />
			<br />
			<fieldset id="service-details">
				<legend>Requirements</legend>
				<label class="noselect"><input type="checkbox" name="hasPhoto" />Photo</label>
			</fieldset>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>

	<div id="edit-form" class="account-form student" title="Edit student info">
		<p class="response">Changes will be automatically saved.</p>
		<form autocomplete="off">
			<input type="text" id="lname" name="lname" maxlength="25" title="Last Name" placeholder="Last Name" class="focusglow" required="" style="width:280px"
			/><input type="text" id="fname" name="fname" maxlength="25" title="First Name" placeholder="First Name" class="focusglow" required="" style="width:280px"
			/><input type="text" id="mname" name="mname" maxlength="4" title="Middle Initial(s)" placeholder="M.I." class="focusglow" required="" style="width:60px"
			/><input type="text" id="course" name="course" maxlength="10" title="Course" placeholder="Course" class="focusglow" required="" style="width:150px"
			/><label>Year:<input type="number" id="year" name="year" title="Year Level" min="1" max="5" value="1" step="1" class="focusglow" required="" style="width:40px" /></label>
			<input type="text" id="address" name="address" maxlength="90" title="Home Address" placeholder="Home Address" class="focusglow" required="" style="width:680px"
			/><input type="tel" id="Contact" name="Contact" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/><input type="text" id="Bday" name="Bday" maxlength="10" title="Birthdate" placeholder="Birthdate" class="focusglow" required="" style="width:204px"
			/><input type="text" id="Age" name="Age" maxlength="2" title="Age" placeholder="Age" readonly="" style="width:40px"
			/><select name="Gender" id="Gender" title="Gender" class="focusglow" required="">
				<option value="" disabled selected>Gender</option>
				<option value="m">Male</option>
				<option value="f">Female</option>
			</select><select name="CivStat" id="CivStat" title="Civil Status" class="focusglow" required="">
				<option value="" disabled selected>Civil Status</option>
				<option value="s">Single</option>
				<option value="m">Married</option>
				<option value="d">Divorced</option>
				<option value="p">Separated</option>
				<option value="w">Widowed</option>
			</select>
			<br />
			<br />
			<input type="text" id="Father" name="Father" maxlength="64" title="Father's Name" placeholder="Father's Name" class="focusglow" required="" style="width:680px"
			/><input type="tel" id="FatherPhone" name="FatherPhone" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/><input type="text" id="Mother" name="Mother" maxlength="64" title="Mother's Name" placeholder="Mother's Name" class="focusglow" required="" style="width:680px"
			/><input type="tel" id="MotherPhone" name="MotherPhone" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:194px"
			/><input type="hidden" id="sid" name="sid" value="0" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<ul id="details-menu" class="menulist">
		<li id="oncampus-menu">On-campus</li>
		<li id="offcampus-menu">Off-campus</li>
	</ul>

	<div id="oncampus-form" class="account-form student" title="On-Campus Community Service">
		<button id="oncampus-button" tabindex="1">+ Add record</button>
		<fieldset id="oncampus-records">
			<legend>Records</legend>
			<table class="noselect records">
				<thead>
					<tr class="table-labels">
						<th>Activity Name</th>
						<th>Category</th>
						<th>Initiator</th>
						<th>School Year</th>
						<th>Semester</th>
						<th>Hours Served</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-rows">
						<td class="t-actname" title="Activity Name" data-id="">Sample long activity name that is long</td>
						<td class="t-category" title="Category" data-id="">Category</td>
						<td class="t-initiator" title="Initiator" data-id="">Initiator</td>
						<td class="t-schoolyear" title="School Year" data-id="">2014 &ndash; 2015</td>
						<td class="t-semester" title="Semester" data-id="">1</td>
						<td class="t-hours" title="Hours" data-id="">4.0</td>
						<td class="t-eval" title="Evaluation" data-id="">OK</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<ul id="actname-menu-on" class="menulist">
			<li class="act-edit-menu">Edit</li>
			<li class="act-sched-menu">Schedule</li>
			<li class="act-delete-menu">Delete</li>
		</ul>
	</div>

	<div id="offcampus-form" class="account-form student" title="Off-Campus Community Service">
		<button id="offcampus-button" tabindex="1">+ Add record</button>
		<fieldset id="offcampus-records">
			<legend>Records</legend>
			<table class="noselect records">
				<thead>
					<tr class="table-labels">
						<th>Activity Name</th>
						<th>Organizer</th>
						<th>Address</th>
						<th>Point Person</th>
						<th>Contact Number</th>
						<th>School Year</th>
						<th>Semester</th>
						<th>Hours Served</th>
						<th>Certificate</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</fieldset>
		<ul id="actname-menu-off" class="menulist">
			<li class="act-edit-menu">Edit</li>
			<li class="act-sched-menu">Schedule</li>
			<li class="act-delete-menu">Delete</li>
		</ul>
	</div>

	<div id="oncampus-add" class="account-form student" title="On-Campus Community Service">
		<form>
			<label for="year">Year:<input type="number" name="schoolyear" title="School Year" min="1900" max="2999" value="1900" class="focusglow year" required="" style="width:70px"
			/><span class="label-span noselect"> &ndash; </span><span class="label-span noselect response" style="color:#000">0000, </span>
			</label><label class="noselect">Semester:<input type="number" name="semester" title="Semester" min="1" max="2" value="0" class="focusglow sem" required="" style="width:40px" /></label>
			<input type="text" name="actname" maxlength="255" title="Name of Activity" placeholder="Name of Activity" class="focusglow" required="" style="width:360px" /><br />
			<select name="category" title="Category" class="focusglow" required="" style="width: 170px;">
				<option value="" disabled selected>Category</option>
				<option value="CDC">CDC</option>
				<option value="Parish">Parish</option>
				<option value="Community">Community</option>
				<option value="Welfare Institution">Welfare Institution</option>
				<option value="others">Others</option>
			</select><input type="text" id="on-category" name="others-category" maxlength="32" title="Category" placeholder="" class="invisible others" disabled="" style="width:180px" /><br />
			<select name="initiator" title="Initiator" class="focusglow" required="" style="width: 170px;">
				<option value="" disabled selected>Initiator</option>
				<option value="Personal">Personal</option>
				<option value="Course Requirement">Course Requirement</option>
				<option value="Program">Program</option>
				<option value="Division">Division</option>
				<option value="Student Organization">Student Organization</option>
				<option value="others">Others</option>
			</select><input type="text" id="on-initiator" name="others-initiator" maxlength="64" title="Initiator" placeholder="" class="invisible others" disabled="" style="width:180px" /><br />
			<fieldset>
				<legend>Requirements</legend>
				<label class="noselect">Hours served:</label><input type="text" id="onhours" name="hours" title="On-Campus Hours" class="invisible hourbox" readonly="" tabindex="-1" style="width:60px"
				/><div id="slider-onhours"></div>
				<!--<label class="noselect"><input type="checkbox" name="eval" />Evaluation</label>-->
			</fieldset>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="offcampus-add" class="account-form student" title="Off-Campus Community Service">
		<form autocomplete="off">
			<label for="year">Year:<input type="number" name="schoolyear" title="School Year" min="1900" max="2999" value="1900" class="focusglow year" required="" style="width:70px"
			/><span class="label-span noselect"> &ndash; </span><span class="label-span noselect response" style="color:#000">0000, </span>
			</label><label class="noselect">Semester:<input type="number" name="semester" title="Semester" min="1" max="2" value="0" class="focusglow sem" required="" style="width:40px" /></label>
			<input type="text" name="actname" maxlength="255" title="Name of Activity" placeholder="Name of Activity" class="focusglow" required="" style="width:360px" /><br />
			<input type="text" name="organizer" maxlength="64" title="Organizer" placeholder="Organizer" class="focusglow" required="" style="width:360px" /><br />
			<input type="text" name="address" maxlength="255" title="Address" placeholder="Address" class="focusglow" required="" style="width:360px" /><br />
			<input type="text" name="pointperson" maxlength="64" title="Point Person" placeholder="Point Person" class="focusglow" required="" style="width:200px"
			/><input type="tel" name="contact" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow" required="" style="width:150px" /><br />
			<fieldset>
				<legend>Requirements</legend>
				<label for="offhours" class="noselect">Hours served:</label><input type="text" id="offhours" name="hours" title="Off-Campus Hours" class="invisible hourbox" readonly="" tabindex="-1" style="width:60px"
				/><div id="slider-offhours"></div>
				<!--<label class="noselect"><input type="checkbox" name="eval" />Evaluation</label>-->
				<label class="noselect"><input type="checkbox" name="cert" />Certificate</label>
			</fieldset>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="schedule-form" class="account-form student" title="Activity Schedule">
		<button id="addsched-button" tabindex="1">+ Add dates</button>
		<fieldset id="schedule-records">
			<legend>Dates</legend>
			<table class="noselect records">
				<thead>
					<tr class="table-labels">
						<th>Start</th>
						<th>End</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</fieldset>
		<ul id="schedule-menu" class="menulist">
			<li class="sched-delete-menu">Delete</li>
		</ul>
	</div>
	
	<div id="schedule-add" class="account-form student" title="Add Date">
		<form autocomplete="off">
			<input type="text" id="sched-start" name="start" maxlength="10" title="Start" placeholder="Start" class="focusglow" required="" style="width:100px"
			/><select name="start-hour" title="Hour" class="focusglow" required="" style="width: 65px;">
				<option value="" disabled selected>Hour</option>
				<?php selectEnumerator(12, false);	?>
			</select><select name="start-minute" title="Minute" class="focusglow" required="" style="width: 75px;">
				<option value="" disabled selected>Minute</option>
				<?php selectEnumerator(59);	?>
			</select><select name="start-ampm" title="Minute" class="focusglow" required="" style="width: 55px;">
				<option value="am" selected>AM</option>
				<option value="pm">PM</option>
			</select><br />
			<input type="text" id="sched-end" name="end" maxlength="10" title="End" placeholder="End" class="focusglow" required="" style="width:100px"
			/><select name="end-hour" title="Hour" class="focusglow" required="" style="width: 65px;">
				<option value="" disabled selected>Hour</option>
				<?php selectEnumerator(12, false);	?>
			</select><select name="end-minute" title="Minute" class="focusglow" required="" style="width: 75px;">
				<option value="" disabled selected>Minute</option>
				<?php selectEnumerator(59);	?>
			</select><select name="end-ampm" title="Minute" class="focusglow" required="" style="width: 55px;">
				<option value="am" selected>AM</option>
				<option value="pm">PM</option>
			</select><br />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>

	<!-- One-click editing forms -->
	<div id="edit-actname-form" class="account-form student" title="Activity Name">
		<form class="quickedit-form" autocomplete="off">
			<input type="text" name="actname" maxlength="255" title="Name of Activity" placeholder="Name of Activity" class="focusglow edit-textbox" required="" style="width:360px" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-category-form" class="account-form student" title="Category">
		<form class="quickedit-form" autocomplete="off">
			<select name="category" title="Category" class="focusglow edit-combobox" required="" style="width: 170px;">
				<option value="" disabled selected>Category</option>
				<option value="CDC">CDC</option>
				<option value="Parish">Parish</option>
				<option value="Community">Community</option>
				<option value="Welfare Institution">Welfare Institution</option>
				<option value="others">Others</option>
			</select><input type="text" name="others-category" maxlength="32" title="Category" placeholder="" class="invisible others edit-textbox" disabled="" style="width:180px" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-initiator-form" class="account-form student" title="Initiator">
		<form class="quickedit-form" autocomplete="off">
			<select name="initiator" title="Initiator" class="focusglow edit-combobox" required="" style="width: 170px;">
				<option value="" disabled selected>Initiator</option>
				<option value="Personal">Personal</option>
				<option value="Course Requirement">Course Requirement</option>
				<option value="Program">Program</option>
				<option value="Division">Division</option>
				<option value="Student Organization">Student Organization</option>
				<option value="others">Others</option>
			</select><input type="text" name="others-initiator" maxlength="64" title="Initiator" placeholder="" class="invisible others edit-textbox" disabled="" style="width:180px" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-organizer-form" class="account-form student" title="Organizer">
		<form class="quickedit-form" autocomplete="off">
			<input type="text" name="organizer" maxlength="64" title="Organizer" placeholder="Organizer" class="focusglow edit-textbox" required="" style="width:360px" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
				
	<div id="edit-address-form" class="account-form student" title="Address">
		<form class="quickedit-form" autocomplete="off">
			<input type="text" name="address" maxlength="255" title="Address" placeholder="Address" class="focusglow edit-textbox" required="" style="width:360px" /><br />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-pointperson-form" class="account-form student" title="Point Person">
		<form class="quickedit-form" autocomplete="off">
			<input type="text" name="pointperson" maxlength="64" title="Point Person" placeholder="Point Person" class="focusglow edit-textbox" required="" style="width:200px"/>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-contact-form" class="account-form student" title="Contact Details">
		<form class="quickedit-form" autocomplete="off">
			<input type="tel" name="contact" maxlength="13" title="Contact Details" placeholder="Contact Details" class="focusglow edit-textbox" required="" style="width:150px" />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-onhours-form" class="account-form student" title="Hours Served">
		<form autocomplete="off">
			<label for="onhours-edit" class="noselect">Hours served:</label><input type="text" id="onhours-edit" name="hours" title="On-Campus Hours" class="invisible hourbox edit-textbox" readonly="" tabindex="-1" style="width:60px"
			/><div id="slider-edit-onhours" class="edit-slider"></div>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-offhours-form" class="account-form student" title="Hours Served">
		<form autocomplete="off">
			<label for="offhours-edit" class="noselect">Hours served:</label><input type="text" id="offhours-edit" name="hours" title="Off-Campus Hours" class="invisible hourbox edit-textbox" readonly="" tabindex="-1" style="width:60px"
			/><div id="slider-edit-offhours" class="edit-slider"></div>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="edit-eval-form" class="account-form student" title="Evaluation">
		<form autocomplete="off" style="text-align: center">
			<button id="eval-request-button" class="noeval" tabindex="1">Generate Evaluation Code</button><input type="text" id="eval-request" name="request" title="Evaluation Code" class="invisible" readonly="" style="width:140px; text-align: center" maxlength="10" tabindex="-1" /><br/>
			<div class="haseval">
				<button id="eval-view-button" tabindex="2">View Evaluation Forms</button>
			</div>
		</form>
	</div>
	
	<div id="view-eval" class="account-form student" title="Evaluation Forms">
		<fieldset id="eval-records">
			<legend>Submitted evaluation forms</legend>
			<table class="noselect records">
				<thead>
					<tr class="table-labels">
						<th>School Year</th>
						<th>Semester</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</fieldset>
		<ul id="eval-menu" class="menulist">
			<li class="eval-view-menu">View</li>
			<li class="eval-delete-menu">Delete</li>
		</ul>
	</div>
	
	<div id="view-eval-form" class="account-form student" title="Evaluation">
		<form autocomplete="off">
			<ol>
				<li>
					<p>What did you like most about the activity/event? Why?</p>
					<textarea id="q1-1" class="focusglow" maxlength="1000" disabled=""></textarea>
				</li>
				<li>
					<p>If you were to describe your experience using one word, it would be...</p>
					<input type="text" id="q2-1" class="focusglow" maxlength="45" style="margin-top: 0px" disabled="" />
				</li>
				<li>
					<p>What Paulinian Core Value was enfleshed/reflected/gained from the activity?</p>
					<select id="q3-1" title="Paulinian Core Values" class="focusglow" disabled="" style="width: 180px;">
						<option value="" disabled>Paulinian Core Values</option>
						<option value="Christ-Centeredness">Christ-Centeredness</option>
						<option value="Charism">Charism</option>
						<option value="Commitment to Mission">Commitment to Mission</option>
						<option value="Community">Community</option>
						<option value="Charity">Charity</option>
					</select>
					<p>
						Please cite some evidences to prove that such Paulinian Core Value was enfleshed/reflected/gained<br />
						<i>(ex. community - found new friends from other academic programs during the feeding)</i>.
					</p>
					<textarea id="q3-2" class="focusglow" maxlength="1000" disabled=""></textarea>
				</li>
				<li>
					<p>What virtue was inculcated in you after the activity?</p>
					<input type="text" id="q4-1" class="focusglow" maxlength="45" style="margin-top: 0px" disabled="" />
				</li>
				<li>
					<p class="preslider">In a scale of 1 to 4, where 4 is the highest, please rate the activity:</p>
					<div id="slider-rate-act" class="rating-slider"></div><input type="text" id="q5-1" title="Activity rating" class="invisible hourbox" disabled="" tabindex="-1" value="4" style="width:20px" />
					<div class="rate-threshold">
						<p>What aspects of the activity/event need to be improved?</p>
						<textarea id="q5-2" class="focusglow" maxlength="1000" disabled=""></textarea>
					</div>
				</li>
				<li>
					<p class="preslider">In a scale of 1 to 4, where 4 is the highest, please rate your participation:</p>
					<div id="slider-rate-part" class="rating-slider"></div><input type="text" id="q6-1" title="Participation rating" class="invisible hourbox" disabled="" tabindex="-1" value="4" style="width:20px" />
					<div class="rate-threshold">
						<p>What do you intend to do to make your participation more active and meaningful?</p>
						<textarea id="q6-2" class="focusglow" maxlength="1000" disabled=""></textarea>
					</div>
				</li>
				<li>
					<p>What insights/realizations did you gain from the activity/event?</p>
					<textarea id="q7-1" class="focusglow" maxlength="1000" disabled=""></textarea>
				</li>
				<li>
					<p>What is your next plan of action?</p>
					<textarea id="q8-1" class="focusglow" maxlength="1000" disabled=""></textarea>
				</li>
			</ol>
		</form>
	</div>
	
	<div id="student-summary" class="account-form student" title="Summary">
		<fieldset id="student-summary-records">
			<legend>All registered students</legend>
			<table class="noselect records">
				<thead>
					<tr class="table-labels">
						<th>Last Name</th>
						<th>First Name</th>
						<th>Middle Initial</th>
						<th>Course</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div id="confirm-delete-form" class="account-form student" title="Confirm delete">
		<form class="quickedit-form" autocomplete="off">
			<input type="text" name="confirm-delete" maxlength="6" title="Type delete to confirm" placeholder="Type &quot;delete&quot; to confirm" class="focusglow" required="" autocomplete="off" style="width:200px"/>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
</body>


</html>


<?php
function selectEnumerator($n, $zero = true, $leadingZero = true) {
	
	for ($i = $zero ? 0 : 1; $i <= $n; $i++) {
		$o = $leadingZero && ($i < 10) ? "0$i" : $i;
		echo "<option value=\"$o\">$o</option>\n";
	}
}