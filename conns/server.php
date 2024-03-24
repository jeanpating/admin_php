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
       	// Check admin table for hashed password
		$query_admin_hashed = "SELECT * FROM users WHERE user_email='$username'";
		$results_admin_hashed = mysqli_query($db, $query_admin_hashed);
		if (mysqli_num_rows($results_admin_hashed) == 1) {
			$row = mysqli_fetch_assoc($results_admin_hashed);
			if (password_verify($password, $row['user_pwd'])) {
				// Admin login with hashed password
				session_start();
				$_SESSION['username1'] = $username;
				header('location: admin.php');
				exit(); 
			}
		}

		// Check admin table for plain text password
		$query_admin_plain = "SELECT * FROM users WHERE user_email='$username' AND user_pwd='$password'";
		$results_admin_plain = mysqli_query($db, $query_admin_plain);
		if (mysqli_num_rows($results_admin_plain) == 1) {
			// Admin login with plain text password
			session_start();
			$_SESSION['username1'] = $username;
			header('location: admin.php');
			exit(); 
		}

        // Check employee table for hashed password
        $query_employee_hashed = "SELECT * FROM emp_acc WHERE emp_email='$username'";
        $results_employee_hashed = mysqli_query($db, $query_employee_hashed);
        if (mysqli_num_rows($results_employee_hashed) == 1) {
            $row = mysqli_fetch_assoc($results_employee_hashed);
            if (password_verify($password, $row['emp_pwd'])) {
                // Employee login with hashed password
                $employeeId = $row['emp_id']; // Get employee ID
                session_start();
                $_SESSION['username'] = $username;
                header("location: employee_dashboard.php?emp_id=$employeeId");
                exit(); 
            }
        }

        // Check employee table for plain text password
        $query_employee_plain = "SELECT * FROM emp_acc WHERE emp_email='$username' AND emp_pwd='$password'";
        $results_employee_plain = mysqli_query($db, $query_employee_plain);
        if (mysqli_num_rows($results_employee_plain) == 1) {
            // Employee login with plain text password
            $row = mysqli_fetch_assoc($results_employee_plain);
            $employeeId = $row['emp_id']; // Get employee ID
            session_start();
            $_SESSION['username'] = $username;
            header("location: employee_dashboard.php?emp_id=$employeeId");
            exit(); 
        }

        // Invalid username/password
        header("location: login.php?wup=Wrong Username / Password&user=".$username."");
    }
}
?>
