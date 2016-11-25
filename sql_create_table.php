<?php
	include('sql_const.php');
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if(!$conn)
	{
		die("Connection failed: " . mysqli_connect_error());
	}
	$sql = "
	CREATE TABLE users
	(
		userID int NOT NULL AUTO_INCREMENT,
		username varchar(32) NOT NULL,
		password varchar(128) NOT NULL,
		fName varchar(32) NOT NULL,
		lName varchar(64) NOT NULL,
		email varchar(64) NOT NULL,
		salt varchar(64) NOT NULL,
		isAdmin boolean DEFAULT FALSE,
		PRIMARY KEY (userID)
	);";
	if (mysqli_query($conn, $sql))
	{
		echo "Users table created!<br />";
		$salt = "c14o9b6e3e8i7i3b3h4";
		$hash = hash(HASH_ALGO, $salt."root");
		$sql = "INSERT INTO users VALUES ('1', 'root', '$hash', 'Root', 'Access', 'root@access.com', '$salt', TRUE);";
		if (mysqli_query($conn, $sql))
		{
			echo "Root access acount created!<br />";
			$sql = "CREATE TABLE content
			(
				contentID int NOT NULL AUTO_INCREMENT,
				contentTitle varchar(128) NOT NULL,
				contentEntry text NOT NULL,
				contentCrtTime timestamp DEFAULT CURRENT_TIMESTAMP,
				contentModTime timestamp DEFAULT CURRENT_TIMESTAMP,
				userID int NOT NULL,
				PRIMARY KEY (contentID),
				FOREIGN KEY (userID) REFERENCES users(userID)
			);";
			if (mysqli_query($conn, $sql))
			{
				echo "Content table created!<br />";
				$sql = "INSERT INTO content(contentID, contentTitle, contentEntry, userID) VALUES
				(
					'1', 'Welcome', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed interdum erat vel arcu euismod, sed ultricies sapien tincidunt. Vestibulum sit amet bibendum odio. Vivamus lobortis nisl eget lacus sagittis fermentum. Sed enim odio, sagittis semper justo id, fermentum lobortis ipsum. Sed molestie erat vel ultricies convallis. Donec placerat libero ex, a consectetur magna mattis vel. Proin ac magna sem. Vivamus imperdiet tempor mi elementum vulputate. Vivamus venenatis enim sapien, vitae rhoncus ex luctus non. Mauris metus urna, lobortis vel varius a, aliquam nec erat. Nulla mollis nibh vitae nulla mattis sagittis. Nullam convallis justo a tincidunt bibendum. Pellentesque enim odio, gravida in sapien sodales, gravida placerat augue. Vestibulum sit amet lectus vitae odio fermentum commodo vitae et quam. Phasellus porttitor, velit vitae congue consectetur, ex purus feugiat purus, ac varius orci purus ac risus.

Donec aliquam tortor vitae dui condimentum scelerisque. Donec commodo porttitor purus. In ipsum nisi, tempor eu orci quis, porttitor rutrum mauris. Phasellus a nibh quis lacus ullamcorper iaculis. Nullam sollicitudin venenatis cursus. Nam luctus magna in scelerisque eleifend. Morbi pharetra efficitur elit at mollis. Phasellus nec purus quis velit luctus ultrices. Duis scelerisque, justo a imperdiet aliquam, tellus velit malesuada elit, in tempus tellus quam non tortor. Maecenas malesuada arcu eu varius pharetra. Aliquam sodales, ex nec maximus feugiat, purus metus finibus ex, a lacinia mi lacus eget mauris. Nullam porta velit ut ex mollis, ac venenatis velit pretium. Curabitur blandit aliquet diam quis sollicitudin. Pellentesque dolor eros, ullamcorper id elit a, lacinia auctor libero. Sed vulputate vitae enim non condimentum. Sed ut mi non diam faucibus vulputate sit amet non nisi.

Ut ante est, placerat eu bibendum condimentum, vehicula in neque. Nullam consectetur mattis elit in placerat. Etiam nisl sem, malesuada ac magna eget, egestas pulvinar nibh. Vivamus sodales nibh eu dolor mattis lacinia. Praesent ultricies rhoncus risus, vitae volutpat metus ultricies ut. Nulla ante felis, venenatis at congue eget, tincidunt aliquet quam. Aenean egestas eleifend venenatis. Nulla eget augue vel lacus vulputate consectetur. Sed pretium purus nec mi pretium sodales. Fusce eu est non dolor viverra ornare. Suspendisse luctus blandit tempor. Duis nec tempus lorem.

Nunc ac tempus leo. Etiam et nisl arcu. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam justo quam, fermentum sit amet arcu vitae, aliquam sodales metus. Aenean molestie euismod metus eget venenatis. Vivamus feugiat urna eu justo eleifend, ut rutrum ligula bibendum. Proin bibendum eleifend augue a imperdiet. Nam et metus elementum, hendrerit urna sit amet, vestibulum dolor. Praesent accumsan enim ligula, non hendrerit neque mollis venenatis.

Vivamus id velit vestibulum odio tristique elementum. Phasellus ac lobortis massa. Aliquam a aliquet mi, vel dapibus tortor. Phasellus finibus urna eu turpis porta, quis ultrices sapien semper. Cras eu felis vitae velit egestas posuere. Nam blandit erat vitae velit vehicula porttitor. Nulla facilisi. Donec pellentesque in magna nec condimentum.', '1'
				);";
				if (mysqli_query($conn, $sql))
				{
					echo "Default article inserted!<br />";
					$sql = "CREATE TABLE content_archive
					(
						contentID int NOT NULL,
						contentTitle varchar(128) NOT NULL,
						contentEntry text NOT NULL,
						contentCrtTime timestamp NOT NULL,
						contentModTime timestamp NOT NULL,
						userID int NOT NULL,
						PRIMARY KEY (contentID),
						FOREIGN KEY (userID) REFERENCES users(userID)
					);";
					if (mysqli_query($conn, $sql))
					{
						echo "Content archive table created!<br />";
						$sql = "CREATE TABLE visits
						(
							visitID int NOT NULL AUTO_INCREMENT,
							visitIP varchar(65) NOT NULL,
							visitIPP varchar(65) DEFAULT NULL,
							visitAgent varchar(60) NOT NULL,
							visitOS varchar(30) NOT NULL,
							visitDate date NOT NULL,
							userID int DEFAULT NULL,
							PRIMARY KEY (visitID),
							FOREIGN KEY (userID) REFERENCES users(userID)
						);";
						if (mysqli_query($conn, $sql))
						{
							echo "Visists table created!<br />";
							if (!is_dir("log"))
							{
								if(mkdir("log"))
									echo "Log directory created!<br />";
								else
									echo "Error: log directory not created!<br />";
							}
							else
								echo "Warning: log directory already exists!<br />";
						}
						else
							echo "Error: ".mysqli_error($conn);
					}
					else
						echo "Error: ".mysqli_error($conn);
				}
				else
					echo "Error: ".mysqli_error($conn);
			}
			else
				echo "Error: ".mysqli_error($conn);
		}
		else
			echo "Error: ".mysqli_error($conn);
	}
	else
		echo "Error: ".mysqli_error($conn);
?>