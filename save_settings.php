<?php
	session_start();
	if(!$_SESSION['admin'])
		echo "Access denied!";
	elseif(isset($_POST['content']))
	{
		$stream = fopen("settings.php", "w");
		if($stream)
		{
			fwrite($stream, $_POST['content']);
			fclose($stream);
			echo "Settings saved!";
		}
		else
			echo "Error: file couldn't be opened!";
	}
?>