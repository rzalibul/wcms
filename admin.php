<?php
/*	if($_SERVER["HTTPS"] != "on")
	{
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}*/
	session_start();
	include('sql_const.php');
	include('settings.php');
//	include('/lib/browser.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin panel</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="jquery-1.10.2.js"></script>
	<script src="ajax_modify.js"></script>
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
			<?php
				if($user_can_post || $_SESSION['admin'])
				{
			?>
			<a class="nav" href="post.php">New post</a>
			<?php
				}
			?>
			<a class="nav" href="index.php">Main page</a>
		</div>
		<div id="wrap">
			<div id="main">
				<?php
					if(!isset($_GET['pos']))
					{
				?>
				<p>Welcome to admin panel!</p>
				<p>Choose what you wish to do from the menu on the right of the screen.</p>
				
				<?php
					}
					else
					{
						switch($_GET['pos'])
						{
							case '1':
							$stream = fopen("settings.php", "r+");
							
				?>
						<textarea class=resizable style="margin-top: 10vh;">
				<?php
						echo fread($stream, filesize("settings.php"));
						fclose($stream);
				?>
						</textarea>
						<button type=button onclick="modify_settings()" style="width: 5vw;">Modify</button>
				<?php
							break;
							case '2':
							$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
							if(!$conn)
								echo "Unable to connect to database!<br />".mysqli_connect_error()."<br />";
							else
							{
								$sql = "SELECT userID, username FROM users WHERE isAdmin = 0;";
								$result = mysqli_query($conn, $sql);
								if($result)
								{
									echo "<table class=users ><tr><td>User ID</td><td>Username</td><td>Elevate?</td></tr>";
									while($row = mysqli_fetch_row($result))
										echo "<tr><td>$row[0]</td><td>$row[1]</td><td><input type=checkbox id=checkbox_$row[0]</td></tr>";
									echo "</table>";
									echo "<button type=button onclick=elevate_to_admin() style=width:5vw;>Update</button>";
								}
							}
				?>
				<?php
							break;
							case '3':
				?>
				<?php
							break;
							default:
				?>
				<?php
						}
					}
				?>
				</div>
			
			<div id="column">
				<a class="admin_nav" href="admin.php?pos=1">Edit settings</a>
				<a class="admin_nav" href="admin.php?pos=2">Elevate user to admin</a>
				<a class="admin_nav" href="admin.php?pos=3">Delete user account</a>
			</div>
		</div>
	</div>
</body>
</html>