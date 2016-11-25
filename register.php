<?php
	session_start();
	include('sql_const.php');

	function test_input($data) 
	{
 		$data = trim($data);
 		$data = stripslashes($data);
 		$data = htmlspecialchars($data);
		return $data;
	}
	
	$date = date("Y-m-d");
	$timestamp = date("Y-m-d H:i:s");
	$path = __DIR__ . "/log/".$date."_log.txt";
	$stream = fopen($path, "a");	
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$valCheck = true;
		if(empty($_POST['username']))
		{
			$_SESSION["username_err"] = "Username is required!";
			$valCheck = false;
		}
		else
		{
			$_SESSION["username_err"] = "";		 
			$username = test_input($_POST['username']);
			if(!preg_match("/^[a-zA-Z0-9]*$/", $username))
			{
				$_SESSION["username_err"] = "Username can only have alphanumeric characters (A-Z and 0-9)";
				$valCheck = false;
			}		
		}
		if(empty($_POST['pwd']))
		{
			$_SESSION["pwd_err"] = "Password is required!";
			$valCheck = false;
		}
		else
		{	
			$_SESSION["pwd_err"] = "";
			$pwd = test_input($_POST['pwd']);
			if(!preg_match("/^[a-zA-Z0-9]*$/", $pwd))
			{
				$_SESSION["pwd_err"] = "Password can only have alphanumeric characters (A-Z and 0-9)";
				$valCheck = false;
			}		
		}
		if(empty($_POST['fname']))
		{
			$_SESSION["fname_err"] = "First name is required!";
			$valCheck = false;
		}
		else
		{
			$_SESSION["fname_err"] = "";
			$fname = test_input($_POST['fname']);
			if(!preg_match("/^[a-zA-Z]*$/", $fname))
			{
				$_SESSION["fname_err"] = "Names can only have alphabetic characters (A-Z)";
				$valCheck = false;
			}		
		}
		if(empty($_POST['lname']))
		{
			$_SESSION["lname_err"] = "Last name is required!";
			$valCheck = false;
		}
		else
		{
			$_SESSION["lname_err"] = "";
			$lname = test_input($_POST['lname']);
			if(!preg_match("/^[a-zA-Z]*$/", $lname))
			{
				$_SESSION["lname_err"] = "Names can only have alphabetic characters (A-Z)";
				$valCheck = false;
			}		
		}
		if(empty($_POST['email']))
		{
			$_SESSION["email_err"] = "E-mail is required!";
			$valCheck = false;
		}
		else
		{
			$_SESSION["email_err"] = "";
			$email = test_input($_POST['email']);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
			{
  				$_SESSION["email_err"] = "Invalid email format";
				$valCheck = false; 
			}
		}
		if(!$valCheck)
		{
			$_SESSION["redirect"] = true;
			$timestamp = date("Y-m-d H:i:s");
			fwrite($stream, PHP_EOL . "<".$timestamp.">Registration validation unsuccessful - IP: ". empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
			fclose($stream);
			header("Location: index.php");
			exit();
		}
		else
		{
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if(!$conn)
			{
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to connect to database!");
				fclose($stream);
				die("Unable to connect to database!");
			}
			$sql = "SELECT username FROM users WHERE username = '$username';";
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0)
			{
				$_SESSION["username_err"] = "User with that username already exists!";
				$_SESSION["redirect"] = true;
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Registration unsuccessful, username ".$username." already exists - IP: ". empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
				fclose($stream);
				mysqli_close($conn);
				header("Location: index.php");
				exit();
			}
			else
			{
				$salt = session_id();
				$hash = hash(HASH_ALGO, $salt.$pwd);
				$sql = "INSERT INTO users(username, password, fName, lName, email, salt) VALUES ('$username', '$hash', '$fname', '$lname', '$email', '$salt');"; 
				if(mysqli_query($conn, $sql))
				{
					$_SESSION["reg_status"] = "Registration successful!";
					$_SESSION["redirect"] = true;
					$timestamp = date("Y-m-d H:i:s");
					fwrite($stream, PHP_EOL . "<".$timestamp.">Registration successful, new user ".$username." registered - IP: ". empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
					fclose($stream);
					header("Location: index.php");
					exit();
				}
				else
				{
					$_SESSION["reg_status"] = "Database error: ".mysqli_error($conn);
					$_SESSION["redirect"] = true;
					$timestamp = date("Y-m-d H:i:s");
					fwrite($stream, PHP_EOL . "<".$timestamp.">Registration unsuccessful, SQL query failed; ".$_SESSION["reg_status"].PHP_EOL . "IP: ". empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
					fclose($stream);
					header("Location: index.php");
					exit();
				}
				mysqli_close($conn);				
			}
		}
	}
?>