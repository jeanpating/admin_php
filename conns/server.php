<?php

// initializing variables
$username = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'employeesdb');



// LOGIN USER
if (isset($_POST['oksbut'])) 
{
  $username = mysqli_real_escape_string($db, $_POST['uname']);
  $password = mysqli_real_escape_string($db, $_POST['upass']);

  if (empty($username)) 
  {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) 
  {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) 
  {
  	
  	$query1 = "SELECT * FROM users WHERE user_email='$username' AND user_pwd='$password'";
  	$results1 = mysqli_query($db, $query1);

  	if (mysqli_num_rows($results1) == 1) 
	{

		session_start();
		$_SESSION['username1'] = $username;
		header('location: admin.php');

	}
    else
    {
        header("location: login.php?wup=Wrong Username / Password&user=".$username."");
    }

  }

}







// LOGIN USER
/*
if (isset($_POST['oksbut'])) 
{
  $username = mysqli_real_escape_string($db, $_POST['uname']);
  $password = mysqli_real_escape_string($db, $_POST['upass']);

  if (empty($username)) 
  {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) 
  {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) 
  {
  	
  	$query1 = "SELECT * FROM users WHERE user_email='$username' AND user_pwd='$password'";
  	$results1 = mysqli_query($db, $query1);

  	if (mysqli_num_rows($results1) == 1) 
	{

		session_start();
		$_SESSION['username1'] = $username;
		header('location: Navbar/MainPOS/POS.php');

	}
	elseif(mysqli_num_rows($results1) == 0)
	{

		$query2 = "SELECT * FROM user_Manager WHERE user_email='$username' AND user_pwd='$password'";
		$results2 = mysqli_query($db, $query2);
  
		if (mysqli_num_rows($results2) == 1) 
		{

		  session_start();
		  $_SESSION['username2'] = $username;
		  header('location: Navbar/MainPOS/POS.php');

		}
		elseif(mysqli_num_rows($results2) == 0)
		{

			$query3 = "SELECT * FROM user_Cashier WHERE user_email='$username' AND user_pwd='$password'";
			$results3 = mysqli_query($db, $query3);
	  
			if (mysqli_num_rows($results3) == 1) 
			{

			  session_start();
			  $_SESSION['username3'] = $username;
			  header('location: Navbar/MainPOS/POS.php');

			}
			else
			{
				header("location: login.php?wup=Wrong Username / Password&user=".$username."");
			}
		}
    }

	}
}
*/
?>