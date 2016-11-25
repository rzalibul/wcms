<?php
/*	if($_SERVER["HTTPS"] != "on")
	{
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}*/
	session_start();
	include('sql_const.php');
	include('settings.php');
	include('/lib/browser.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Blog on technology</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script src="jquery-1.10.2.js"></script>
		<script src="encoder.js"></script>
		<script src="validation.js"></script>
		<script src="ajax_modify.js"></script>
	</head>
	<body>
	<?php

		if(isset($_SESSION["redirect"]))
		{
			if(!($_SESSION["redirect"] || $_SESSION["logged"]))
			{
				session_unset();
			//	session_destroy();
				$_SESSION["redirect"] = false;
			}
		}
		else
			$_SESSION["redirect"] = false;
		if(!isset($_SESSION["logged"]))
		{
			$_SESSION["logged"] = false;
		}
		if(!$_SESSION["logged"])
		{
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($conn)
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
					$sql = "INSERT INTO visits(visitIP, visitIPP, visitAgent, visitOS, visitDate) VALUES('$ip', '$ipp', '$agent', '$os', curdate());";
				else
					$sql = "INSERT INTO visits(visitIP, visitAgent, visitOS, visitDate) VALUES('$ip', '$agent', '$os', curdate());";
				$result = mysqli_query($conn, $sql);
				$_SESSION['visited'] = true;
			}
		
	?>
		<table>
			<tr>
			<?php
				if($_SESSION["redirect"])
				{
			?>
				<td class="redir">
					<h2 id="login_title" style="top: -301.5px;">Login</h2>
				</td>
				<td class="redir">
					<h2 id="register_title" style="top: -301.5px;">Register</h2>
				</td>
			<?php
				}
				else
				{
			?>
				<td class="title" onclick="transition(this)">
					<h2 id="login_title">Login</h2>
				</td>
				<td class="title" onclick="transition(this)">
					<h2 id="register_title">Register</h2>
				</td>
			<?php
				}
			?>
			</tr>
			<tr>
				<td id="loginbox">
					<form id="login" name="login" action="login.php" enctype="multipart/form-data" method="post">
						<p>Username:</p><input type="text" name="username" required /><span class="error"><?php if(isset($_SESSION["userlog_err"])) echo $_SESSION["userlog_err"];?></span>
						<p>Password:</p><input type="password" name="pwd" required /><span class="error"><?php if(isset($_SESSION["pwdlog_err"])) echo $_SESSION["pwdlog_err"];?></span><br />
						<input id="submit" type="submit" value="Login" /><span class="error"><?php if(isset($_SESSION["login_err"])) echo $_SESSION["login_err"];?></span>
					</form>
				</td>
				<td id="registerbox">
					<form id="register" name="register" action="register.php" enctype="multipart/form-data" method="post">
						<p>Username:</p><input type="text" name="username" onchange="validate(this)" required /><span id="username" class="error"><?php if(isset($_SESSION["username_err"])) echo $_SESSION["username_err"];?></span>
						<p>Password:</p><input type="password" name="pwd" onchange="validate(this)" required /><span id="pwd" class="error"><?php if(isset($_SESSION["pwd_err"])) echo $_SESSION["pwd_err"];?></span>
						<p>First name:</p><input class="txt" type="text" name="fname" onchange="validate(this)" required /><span id="fname" class="error"><?php if(isset($_SESSION["fname_err"])) echo $_SESSION["fname_err"];?></span>
						<p>Last name:</p><input class="txt" type="text" name="lname" onchange="validate(this)" required /><span id="lname" class="error"><?php if(isset($_SESSION["lname_err"])) echo $_SESSION["lname_err"];?></span>
						<p>E-mail:</p><input class="txt" type="email" name="email" onchange="validate(this)" required /><span id="email" class="error"><?php if(isset($_SESSION["email_err"])) echo $_SESSION["email_err"];?></span><br />
						<input id="submit" type="submit" value="Register"  /><span id="register" class="error"><?php if(isset($_SESSION["reg_status"])) echo $_SESSION["reg_status"];?></span>
					</form>
				</td>
			</tr>
		</table>
	<?php
		$_SESSION["redirect"] = false;
		}
		else
		{
	?>
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
						if(!isset($_GET['art']))
						{
							$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
							if(!$conn)
								echo "Unable to connect to database!<br />".mysqli_connect_error()."<br />";
							else
							{
								$userid = $_SESSION['userid'];
								if(!isset($_GET['start']))
								{
									$sql = "SELECT content.contentID, content.contentTitle, content.contentEntry, content.contentCrtTime, content.contentModTime, users.fName, users.lName, users.userID 
									FROM content 
									JOIN users ON content.userID = users.userID
									ORDER BY content.contentCrtTime DESC
									LIMIT 0, $articles_per_page;";
								}
								else
								{
									$start = (int) $_GET['start'];
									$end = $start + $articles_per_page;
									$sql = "SELECT content.contentID, content.contentTitle, content.contentEntry, content.contentCrtTime, content.contentModTime, users.fName, users.lName, users.userID 
									FROM content 
									JOIN users ON content.userID = users.userID
									ORDER BY content.contentCrtTime DESC
									LIMIT $start, $end;";
								}
								$result = mysqli_query($conn, $sql);
								if(mysqli_num_rows($result) > 0)
								{
									while($row = mysqli_fetch_row($result))
									{
										if(strlen($row[2]) > $char_truncate)
										{
											$trun = substr($row[2], -(strlen($row[2]) - $char_truncate));
											$trun = nl2br($trun);
											$row[2] = substr($row[2], 0, $char_truncate);				
											$row[2] = nl2br($row[2]);
											echo "<div class=article_wrap>
													<div class=article_title>$row[1]</div>
													<div class=article_content>$row[2]<span class=hidden>$trun</span>...</div>												
													<div class=article_date>
														<a class=article_link href='index.php?art=$row[0]'>Show article</a><br />
														Date created: $row[3]<br />Date modified: $row[4]<br />
														Created by: $row[5]&nbsp;$row[6]
													</div>";
										}
										else
										{
											$row[2] = nl2br($row[2]);
											echo "<div class=article_wrap>
														<div class=article_title>$row[1]</div>
														<div class=article_content>$row[2]</div>												
														<div class=article_date>
															<a class=article_link href='index.php?art=$row[0]'>Show article</a><br />
															Date created: $row[3]<br />Date modified: $row[4]<br />
															Created by: $row[5]&nbsp;$row[6]
														</div>";
										}
										if ($_SESSION['admin'] || $row[7] == $_SESSION['userid'])
											echo "<div class=article_modify><button type=button onclick=modify_content($row[0])>Modify</button><button type=button onclick=delete_content($row[0])>Delete</button></div>";
										echo "</div>";
									}
									$sql = "SELECT COUNT(*) AS total FROM content;";
									$result = mysqli_query($conn, $sql);
									$data = mysqli_fetch_assoc($result);
									$total = (int) $data['total'];
									if ($total > $articles_per_page)
									{
										echo "<div id=pagebar>
													<ul class=pagination>";
										if(isset($start))
										{
											if($start % $articles_per_page != 0)
											{
												$start -= $start % $articles_per_page;
											}
											if($start >= $articles_per_page)
											{
												$start -= $articles_per_page;
												echo "<li><a href='index.php?start=$start'>&lt;&lt;</a></li>";
												$start += $articles_per_page;
											}
										}
										else
											$start = 0;
										$art_per_page = $total / $articles_per_page;										
										for($i = 0; $i <= $art_per_page; $i++)
										{
											$pos = $articles_per_page*$i++;
											if($start != $pos)
												echo "<li><a href='index.php?start=$pos'>$i</a></li>";
											else
												echo "<li><a class='active' href='index.php?start=$pos'>$i</a></li>";
											$i--;
										}
										if(isset($start))
										{
											if($total - $start > $articles_per_page)
											{	
												$start += $articles_per_page;
												echo "<li><a href='index.php?start=$start'>&gt;&gt;</a></li>";
											}
										}
										else
										{
											$start += $articles_per_page;
											echo "<li><a href='index.php?start=$start'>&gt;&gt;</a></li>";
										}
										echo "</ul></div>";
									}
								}
							}
						}
						else
						{
							$art = (int) $_GET['art'];
							$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
							if(!$conn)
								echo "Unable to connect to database!<br />".mysqli_connect_error()."<br />";
							else
							{
								$sql = "SELECT content.contentID, content.contentTitle, content.contentEntry, content.contentCrtTime, content.contentModTime, users.fName, users.lName 
								FROM content 
								JOIN users ON content.userID = users.userID
								WHERE content.contentID = '$art';";
								$result = mysqli_query($conn, $sql);
								if (mysqli_num_rows($result) == 1)
								{
									$row = mysqli_fetch_row($result);
									$row[2] = nl2br($row[2]);
									echo "<div class=article_wrap>
													<a class=back_link href='index.php'>&lt;&lt;&nbsp;Back</a>
													<div class=article_title>$row[1]</div>
													<div class=article_content>$row[2]</div>
													<div class=article_date>
														Date created: $row[3]<br />Date modified: $row[4]<br />
														Created by: $row[5]&nbsp;$row[6]
													</div>
											  </div>";
								}
								else
									echo "Unable to find an article!<br />";
								
							}
						}
					?>
				</div>
				<div id="column">
				<?php
					if(!isset($conn))
						$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
					if(!$conn)
						echo "Unable to connect to datbaase!<br />".mysqli_connect_error()."<br />";
					else
					{
						$sql = "SELECT DISTINCT visitIP FROM visits;";
						$result = mysqli_query($conn, $sql);
						if($result)
						{
							$unique_visits = mysqli_num_rows($result);
						}
						$userid = $_SESSION['userid'];
						$sql = "SELECT COUNT(*) AS total FROM visits WHERE userID = '$userid' AND visitDate = curdate();";
						$result = mysqli_query($conn, $sql);
						if($result)
						{
							$data = mysqli_fetch_assoc($result);
							$user_visits = (int) $data['total'];
						}
					}
					$browser = new Browser();
					$agent = $browser->getBrowser() . " v" . $browser->getVersion();
					$os = $browser->getPlatform();
				?>
					<p>Welcome!</p>
					<p>This site has <?php echo $unique_visits; ?> unique visits.</p>
					<p>You have visited this site <?php echo $user_visits; ?> times today.</p>
					<p>You are using a <?php echo $agent; ?> browser and <?php echo $os; ?> operating system.</p>
					<p id=resolution>Cannot determine screen resolution - JavaScript disabled or not working</p><script>$("p#resolution").html("Your screen resolution is " + window.screen.width + " x " + window.screen.height);</script>
				<?php
				?>
				</div>
			</div>
		</div>
	<?php
		}
	?>
	</body>
</html>