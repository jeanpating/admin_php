<?php
// initializing variables
$username = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'employeesdb');

// LOGIN USER
if (isset($_POST['oksbut'])) {
    $username = mysqli_real_escape_string($db, $_POST['uname']);
    $password = mysqli_real_escape_string($db, $_POST['upass']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
       	// Check admin table
		$query1 = "SELECT * FROM users WHERE user_email='$username' AND user_pwd='$password'";
		$results1 = mysqli_query($db, $query1);

        // Check employee table
        $query_employee = "SELECT * FROM emp_acc WHERE emp_email='$username' AND emp_pwd='$password'";
        $results_employee = mysqli_query($db, $query_employee);

		if (mysqli_num_rows($results1) == 1) {
		// Admin login
		session_start();
		$_SESSION['username1'] = $username;
		header('location: admin.php');
  
        } elseif (mysqli_num_rows($results_employee) == 1) {
            // Employee login
			$row = mysqli_fetch_assoc($results_employee); 
			$employeeId = $row['emp_id']; // Get employee ID
			session_start();
			$_SESSION['username'] = $username;
			header("location: employee_dashboard.php?emp_id=$employeeId");
			exit(); 
        } else {
            // Invalid username/password
            header("location: login.php?wup=Wrong Username / Password&user=".$username."");
        }
    }
}
?>
