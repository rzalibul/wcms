<?php
	session_start();
	include('sql_const.php');
	$date = date("Y-m-d");
	$path = __DIR__ . "/log/".$date."_log.txt";
	$stream = fopen($path, "a");
	if(!$_SESSION['admin'] && isset($_POST['contentid']))
	{
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if($conn)
		{
			$id = (int) $_POST['contentid'];
			$sql = "SELECT userID FROM content WHERE contentID = '$id';";
			$result = mysqli_query($conn, $sql);
			if($result)
			{
				$row = mysqli_fetch_row($result);
				$userid = (int) $row[0];
				if($userid != $_SESSION['userid'])
				{
					$timestamp = date("Y-m-d H:i:s");
					fwrite($stream, PHP_EOL . "<".$timestamp.">Access denied for edition! User IDs (Creator: ".$_SESSION['userid'].", executor: ".$userid.")"." don't match"." - IP: ".empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
					fclose($stream);
					echo "Access denied!";
					exit();
				}
			}
			else
			{
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Article not found! Content ID: ".$id);
				fclose($stream);
				echo "Post not found!";
				exit();
			}
		}
		else
		{
			$timestamp = date("Y-m-d H:i:s");
			fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to connect to database!\n".mysqli_connect_error());
			fclose($stream);
			echo "Unable to connect to database!";
			exit();
		}
	}
	if(isset($_POST['contentid']) && isset($_POST['content']))
	{
		if(!isset($conn))
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if($conn)
		{
			$content = mysqli_real_escape_string($conn, htmlentities($_POST['content']));
			$id = (int) $_POST['contentid'];
			$sql = "UPDATE content SET contentEntry='$content', contentModTime=now() WHERE contentID = '$id';";
			$result = mysqli_query($conn, $sql);
			if($result)
			{
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Article edited by user ".$_SESSION['username']." (User ID: ".$_SESSION['userid']." )");
				fclose($stream);
				echo "Article modified!";
			}
			else
			{
				$timestamp = date("Y-m-d H:i:s");
				fwrite($stream, PHP_EOL . "<".$timestamp.">Edition unsuccessful - SQL query failed ".mysqli_error($conn));
				fclose($stream);
				echo "Edition unsuccessful!";
			}
			mysqli_close($conn);
		}
		else
		{
			$timestamp = date("Y-m-d H:i:s");
			fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to connect to database!\n".mysqli_connect_error());
			fclose($stream);
			echo "Unable to connect to database!";
		}
	}
	else
	{
		$timestamp = date("Y-m-d H:i:s");
		fwrite($stream, PHP_EOL . "<".$timestamp.">No data received! - IP: ".empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']);
		fclose($stream);
		echo "No data received!";
	}
?>