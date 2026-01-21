<?php
date_default_timezone_set('Asia/Karachi');
header_remove(); 
session_start();

function verifyCSRF (){
    if(empty($_SESSION['csrf_token']) || empty($_POST['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        jerror("Invalid CSRF Token");
    }
}

require './datahandling/dbconnect.php';


// Helper: JSON response
include './datahandling/jsonresponses.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if($action==='createCSRF'){
    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
$login_exempt = ['login'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!in_array($_POST['action'],$login_exempt)){
        verifyCSRF();
    }
}

//Login/logout
include './datahandling/handlelogin.php';

// Hierarchy handling file
include './datahandling/handlehierarchy.php';

// Notifications fetching file
include './datahandling/fetchnotifications.php';

//Mark read and unread notifications file
include './datahandling/mark_read.php';

//Employees data handling file
include './datahandling/handleemp.php';

include './datahandling/fetchroles.php';

//Designations handling file
include './datahandling/handledesign.php';

//File for charts handling
include './datahandling/fetchchartsdata.php';


//User's data
include './datahandling/userdata.php';

//download employees list
include './datahandling/downloademplist.php';

//Change Password
include './datahandling/changepass.php';

//bar-chart Bars Data Weekly
include './datahandling/barchartweekly.php';

//bar-chart Bars Data Monthly
include './datahandling/barchartmonthly.php';

jerror('Unknown action.');