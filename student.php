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
	<link rel="stylesheet" href="/css/jquery-ui.min.css">
	<link rel="stylesheet" href="/css/cdc.css">
	<script src="/script/jquery-1.11.1.min.js"></script>
	<script src="/script/jquery-ui.min.js"></script>
	<script src="/script/jquery.tablesorter.min.js"></script>
	<!--<script src="/script/prescript.js"></script>-->
	<script src="/script/prescript.min.js"></script>
</head>

<body class="cdc-student">
	<header class="noselect">
		<nav>
			<div class="constrain">
				<ul id="top-nav">
					<li class="other-nav"><a href="http://cdc.spumanila.edu.dev"><span>Faculty</span></a>
					</li><li class="current-nav"><a href="http://cdc.spumanila.edu.dev/student/"><span>Student</span></a></li>
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
		<div id="container" class="constrain">
			<div id="tabs">
				<ul>
					<li><a href="/student/#tabs-1">Evaluation</a></li>
					<!--<li><a href="/student/#tabs-2">Status</a></li>-->
				</ul>
				<div id="tabs-1">
					<button id="evaluate" tabindex="1">Evaluate</button>
				</div>
<!--				<div id="tabs-2">
					Coming Soon&trade;.
				</div>-->
			</div>
		</div>
	</div>
	
	<div id="footer">
		
	</div>

	<?php echo_common_forms();	?>
	
	<div id="get-eval-form" class="account-form student haseval" title="Enter evaluation code">
		<form autocomplete="off">
			<input type="text" id="evalcode-post" class="focusglow allcaps" name="evalcode" title="Evaluation code" placeholder="Enter code" style="width:140px; text-align: center;" maxlength="10" tabindex="1" /><br />
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
	
	<div id="eval-form" class="account-form student" title="Evaluation">
		<form autocomplete="off">
			<ol>
				<li>
					<p>What did you like most about the activity/event? Why?</p>
					<textarea name="q1-1" class="focusglow" maxlength="1000" required=""></textarea>
				</li>
				<li>
					<p>If you were to describe your experience using one word, it would be...</p>
					<input type="text" name="q2-1" class="focusglow" maxlength="45" style="margin-top: 0px" required="" />
				</li>
				<li>
					<p>What Paulinian Core Value was enfleshed/reflected/gained from the activity?</p>
					<select name="q3-1" title="Paulinian Core Values" class="focusglow" required="" style="width: 180px;">
						<option value="" disabled selected>Paulinian Core Values</option>
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
					<textarea name="q3-2" class="focusglow" maxlength="1000" required=""></textarea>
				</li>
				<li>
					<p>What virtue was inculcated in you after the activity?</p>
					<input type="text" name="q4-1" class="focusglow" maxlength="45" style="margin-top: 0px" required="" />
				</li>
				<li>
					<p class="preslider">In a scale of 1 to 4, where 4 is the highest, please rate the activity:</p>
					<div id="slider-rate-act" class="rating-slider"></div><input type="text" name="q5-1" title="Activity rating" class="invisible hourbox" readonly="" tabindex="-1" value="4" style="width:20px" />
					<div class="rate-threshold">
						<p>What aspects of the activity/event need to be improved?</p>
						<textarea name="q5-2" class="focusglow" maxlength="1000" required=""></textarea>
					</div>
					<input type="hidden" class="changedummy" value="0" />
				</li>
				<li>
					<p class="preslider">In a scale of 1 to 4, where 4 is the highest, please rate your participation:</p>
					<div id="slider-rate-part" class="rating-slider"></div><input type="text" name="q6-1" title="Participation rating" class="invisible hourbox" readonly="" tabindex="-1" value="4" style="width:20px" />
					<div class="rate-threshold">
						<p>What do you intend to do to make your participation more active and meaningful?</p>
						<textarea name="q6-2" class="focusglow" maxlength="1000" required=""></textarea>
					</div>
					<input type="hidden" class="changedummy" value="0" />
				</li>
				<li>
					<p>What insights/realizations did you gain from the activity/event?</p>
					<textarea name="q7-1" class="focusglow" maxlength="1000" required=""></textarea>
				</li>
				<li>
					<p>What is your next plan of action?</p>
					<textarea name="q8-1" class="focusglow" maxlength="1000" required=""></textarea>
				</li>
			</ol>
			<input type="submit" tabindex="-1" class="dummy-submit">
		</form>
	</div>
</body>



</html>