<?php
chdir('..');
date_default_timezone_set('Asia/Manila');
require 'includes/functions.php';
init_session();
session_destroy();
kill_my_cookie();

echo 'Successfully logged out.';