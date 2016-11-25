<?php
	include('sql_const.php');
	include('lib/browser.php');
	function test_input($data) 
	{
 		$data = trim($data);
 		$data = stripslashes($data);
 		$data = htmlspecialchars($data);
		return $data;
	}
	$date = date("Y-m-d");
	$timestamp = date("Y-m-d H:i:s");
	$ip = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
	$path = __DIR__ . "/log/".$date."_log.txt";
	$stream = fopen($path, "a");	
	session_start();
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$valCheck = true;
		if(empty($_POST['username']))
		{
			$_SESSION["userlog_err"] = "Username is required!";
			$valCheck = false;
		}
		else
		{
			$_SESSION["username_err"] = "";		 
			$username = test_input($_POST['username']);
			if(!preg_match("/^[a-zA-Z0-9]*$/", $username))
			{
				$_SESSION["userlog_err"] = "Username can only have alphanumeric characters (A-Z and 0-9)";
				$valCheck = false;
			}		
		}
		if(empty($_POST['pwd']))
		{
			$_SESSION["pwdlog_err"] = "Password is required!";
			$valCheck = false;
		}
		else
		{	
			$_SESSION["pwdlog_err"] = "";
			$pwd = test_input($_POST['pwd']);
			if(!preg_match("/^[a-zA-Z0-9]*$/", $pwd))
			{
				$_SESSION["pwdlog_err"] = "Password can only have alphanumeric characters (A-Z and 0-9)";
				$valCheck = false;
			}		
		}
		if($valCheck)
		{
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if(!$conn)
			{
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to connect to database!");
				fclose($stream);
				die("Unable to connect to database!");
			}
			$sql = "SELECT salt, password FROM users WHERE username='$username';";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result) != 1)
			{
				$_SESSION["logged"] = false;
				$_SESSION["redirect"] = true;
				$_SESSION["login_err"] = "Incorrect username or password!";
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">".$_SESSION["login_err"]." IP: ".$ip);
				fclose($stream);
				header("Location: index.php");
				exit();
			}
			$row = mysqli_fetch_row($result);
			$salt = $row[0];
			$pwd_hash = $row[1];
			$hash = hash(HASH_ALGO, $salt.$pwd);
			
/*			$sql = "SELECT fName, lName FROM users WHERE username='$username' AND password='$pwd';";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result) == 1)
			{
				$row = mysqli_fetch_row($result);
				$_SESSION["username"] = $username;
				$_SESSION["fname"] = $row[0];
				$_SESSION["lname"] = $row[1];
				$_SESSION["logged"] = true;
				header("Location: index.php");
				exit();
			}
*/
			if($pwd_hash == $hash)
			{
				$sql = "SELECT userID, fName, lName, isAdmin FROM users WHERE username='$username';";
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_row($result);
				$_SESSION["username"] = $username;
				$_SESSION["userid"] = (int) $row[0];
				$_SESSION["fname"] = $row[1];
				$_SESSION["lname"] = $row[2];
				$_SESSION["admin"] = (bool) $row[3];
				$_SESSION["logged"] = true;
				$_SESSION["redirect"] = true;
				if($_SESSION['visited'])
				{
					if(empty($_SERVER['HTTP_X_FORWARDED_FOR']))
					{
						$ip = $_SERVER['REMOTE_ADDR'];
						$ipp = null;
					}
					else
					{
						$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
						$ipp = $_SERVER['REMOTE_ADDR'];
					} 
					$browser = new Browser();
					$agent = $browser->getBrowser() . " v" . $browser->getVersion();
					$os = $browser->getPlatform();
					if($ipp != null)
						$sql = "UPDATE visits SET userID = '$row[0]' WHERE visitIP = '$ip' AND visitIPP = '$ipp' AND visitAgent = '$agent' AND visitOS = '$os';";
					else
						$sql = "UPDATE visits SET userID = '$row[0]' WHERE visitIP = '$ip' AND visitAgent = '$agent' AND visitOS = '$os';";
					$result = mysqli_query($conn, $sql);
				}
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Login successful for user: ".$username." - IP: ".empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
				fclose($stream);
				header("Location: index.php");
				exit();
			}
			else
			{
				$_SESSION["logged"] = false;
				$_SESSION["redirect"] = true;
				$_SESSION["login_err"] = "Incorrect username or password!";
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">".$_SESSION["login_err"]." Username: ".$username." - IP: ".empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
				fclose($stream);
				header("Location: index.php");
				exit();
			}
		}
		else
		{
			$_SESSION["logged"] = false;
			$_SESSION["redirect"] = true;
			$timestamp = date("Y-m-d H:i:s");
			fwrite($stream, PHP_EOL . "<".$timestamp.">Login validation unsuccessful - IP: ".empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
			fclose($stream);
			header("Location: index.php");
			exit();
		}
	}
?>