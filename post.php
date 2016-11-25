<?php
	session_start();
	include('sql_const.php');
	include('settings.php');
	if(!$user_can_post)
	{
		if(!$_SESSION['admin'])
		{
			header("Location: index.php");
			exit();
		}
	}
	if(!isset($_SESSION['logged']))
	{
		header("Location: index.php");
		exit();
	}
	elseif(!$_SESSION['logged'])
	{
		header("Location: index.php");
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Post an article</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div id="main_wrap">
			<div id="login_info">
					<span id="welcome">Welcome <?php echo $_SESSION['fname']." ".$_SESSION['lname'];?></span>
						<a class="nav" href="logout.php">Logout</a>
						<?php
							if($_SESSION["admin"])
							{
						?>
								<a class="nav" href="admin.php">Admin</a>
						<?php
							}
						?>
						<a class="nav" href="post.php">New post</a>
						<a class="nav" href="index.php">Main page</a>
			</div>
			<div id="wrap">			
				<div id="main">
					<?php					
					if(!(isset($_POST['title']) || isset($_POST['content'])))
					{						
					?>
						<form id="new_art" action="" method="post">
							<p>Title:</p><input type="text" name="title" id="title" required />
							<p>Content:</p><textarea class="resizable" name="content"></textarea><br />
							<input type="submit" value="Post" />
						</form>
					<?php
					}
					else
					{
						$date = date("Y-m-d");
						$path = __DIR__ . "/log/".$date."_log.txt";
						$stream = fopen($path, "a");
						$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
						if($conn)
						{
							$title = mysqli_real_escape_string($conn, $_POST['title']);
							$content = mysqli_real_escape_string($conn, $_POST['content']);
							$userid = (int) $_SESSION['userid'];
							$sql = "INSERT INTO content(contentTitle, contentEntry, userID) VALUES (\"$title\", \"$content\", \"$userid\");";
							if(mysqli_query($conn, $sql))
							{
								$timestamp = date("Y-m-d H:i:s");
								fwrite($stream, PHP_EOL . "<".$timestamp.">Article posted by user: ".$_SESSION['username']);
								fclose($stream);
								header("Location: index.php");
								exit();
							}
							else
							{
								$timestamp = date("Y-m-d H:i:s");
								fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to post an article by user: ".$_SESSION['username']);
								fclose($stream);
								echo "Unable to post an article!<br />";
							}						
						}
						else
						{
							$timestamp = date("Y-m-d H:i:s");
							fwrite($stream, PHP_EOL . "<".$timestamp.">Unable to connect to database!");
							fclose($stream);
							echo "Unable to connect to database!<br />";
						}
					}
					?>
				</div>
				<div id="column">
				</div>
			</div>
	</body>
</html>