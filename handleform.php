<?php
date_default_timezone_set('Asia/Karachi');
error_reporting(E_ALL); 
ini_set('display_errors', 1);
// handleform.php
header_remove(); // ensure we can set content-type per response below
session_start();


require './datahandling/dbconnect.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Helper: JSON response
include './datahandling/jsonresponses.php';

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