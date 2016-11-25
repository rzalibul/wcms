<?php
	session_start();
	if(!isset($_SESSION['logged']))
	{
		header("Location: index.php");
		exit();
	}
	
	$date = date("Y-m-d");
	$path = __DIR__ . "/log/".$date."_log.txt";
	$stream = fopen($path, "a");
	$timestamp = date("Y-m-d H:i:s");
	$ip = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
	fwrite($stream, PHP_EOL . "<".$timestamp.">User ".$_SESSION['username']." (ID: ".$_SESSION['userid'].") has logged out! - IP: ".$ip);
	fclose($stream);
	
	session_unset();
	session_destroy();
	
	header("Location: index.php");
	exit();
?>