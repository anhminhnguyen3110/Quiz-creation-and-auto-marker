<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8" />
    <meta name="description" content="COS10026 Assignment 2" />
    <meta name="keywords" content="HTML, CSS, JavaScript" />
    <meta name="author" content="React Lions" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/style.css"/>
    <link rel="icon" href="images/react.svg">
    <title>Document</title>
</head>

<body>
	<!--Header(with menu)-->
    <?php 
		session_start();
		if(isset($_SESSION['ADMIN'])){
			header('location: manage.php');
		}
        include ("header.inc");
        include ("menu.inc");
        echo menu("loginAdmin");
        echo "</header>"
    ?>
	<article class='login-main'>
	<?php
	$errorHandler = "";
	function sanitise_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
	
	function loginSecurityHandler($usernameInput,$username, $conn){
		$sql_table = 'logSecurity';
		$query = "SELECT * FROM $sql_table";
		$createdAt = "CREATED_AT";
		$attemptTime = "ATTEMPT_TIME";
		$result = mysqli_query($conn, $query);
		if($result) {
			$create_table_query = "CREATE TABLE $sql_table( 
				$username VARCHAR (30) NOT NULL,
				$createdAt INT NOT NULL,
				$attemptTime INT NOT NULL,
				FOREIGN KEY ($username) REFERENCES admin($username)
			)";
			$result = mysqli_query($conn, $create_table_query);
		}
		$query = "SELECT * FROM $sql_table WHERE $username = '$usernameInput'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		if(!isset($row[$attemptTime])){
			$tmpTime = time();
			$query = "INSERT INTO $sql_table VALUES ('$usernameInput', $tmpTime, 1);";
			$result = mysqli_query($conn, $query);
		}else if(time() - $row[$createdAt] >= 300){
			$tmpTime = time();
			$query = "UPDATE $sql_table
			SET $createdAt = $tmpTime,$attemptTime=1
			WHERE $username = '$usernameInput'
			";
			$result = mysqli_query($conn, $query);
		}else if($row[$attemptTime]<3){
			$tmpAttempt = $row[$attemptTime] + 1;
			$query = "UPDATE $sql_table
			SET $attemptTime = $tmpAttempt 
			WHERE $username = '$usernameInput'
			";
			$tmpAttempt = 3 - $tmpAttempt;
			$GLOBALS['errorHandler'] = "Attempt left: $tmpAttempt, Bad credentail !";
			$result = mysqli_query($conn, $query);
		}else if($row[$attemptTime]==3){
			$GLOBALS['errorHandler'] = "Maximum of attempt to login this account";
		}
	}

	function handleLogin($conn, $sql_table, $username){
        $usernameInput = sanitise_input($_POST['usernameAdmin']);
        $passwordInput = sanitise_input($_POST['passwordAdmin']);
		$attemptTime = "ATTEMPT_TIME";
		$createdAt = "CREATED_AT";
		if(empty($passwordInput)){
			$GLOBALS['errorHandler'] = "Invalid password";
			return;
		}
		$usernameSQuery = "SELECT * FROM $sql_table WHERE $username = '$usernameInput' LIMIT 1";
		$result = mysqli_query($conn, $usernameSQuery);
		$res = mysqli_fetch_assoc($result);
		if(!$res){
			$GLOBALS['errorHandler'] = "No username is provided";
			return;
		}
		if($res['PASSWORD'] != $passwordInput){
			$GLOBALS['errorHandler'] = "Incorrect password!";
			loginSecurityHandler($usernameInput,$username, $conn);
			return;
		}
		
		$sql_table = 'logSecurity';
		$query = "SELECT * FROM $sql_table WHERE $username = '$usernameInput'";
		$results = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($results);
		if($row){
			if($row[$attemptTime] == 3){
				if(time()-$row[$createdAt] >= 300){
					$tmpTime = time();
					$query = "UPDATE $sql_table
					SET $createdAt = $tmpTime,$attemptTime=0
					WHERE $username = '$usernameInput'
					";
					mysqli_query($conn, $query);
				}else{
					$GLOBALS['errorHandler'] = "Maximum of attempt to login this account";
					return;
				}
			}else{
				$tmpTime = time();
				$query = "UPDATE $sql_table
				SET $createdAt = $tmpTime,$attemptTime=0
				WHERE $username = '$usernameInput'
				";
				mysqli_query($conn, $query);
			}
		}
		session_unset();
		$res = mysqli_fetch_assoc($result);
		$_SESSION["ADMIN"] = $usernameInput;	
		$_SESSION["time"] = time();
		$query = "DELETE FROM $sql_table WHERE $username = '$usernameInput'";
		$results = mysqli_query($conn, $query);
		header('location: manage.php');
    }
	if(isset($_POST['usernameAdmin']) || isset($_POST['passwordAdmin']) ){
		$errorHandler = "";
		$servername = "feenix-mariadb.swin.edu.au";
		$host = "s103515617";
		$password = "reactjs";
		$dbname = "s103515617_db";
		$sql_table = "admin";
		$username = "STUDENT_ID";


        $userID = "USER_ID";
        $usernameAdmin = "USER_NAME";
		$passwordAdmin = "PASSWORD";
		$conn = mysqli_connect($servername, $host, $password, $dbname);
		if($conn) {
			echo "<p>Connection failed: " . mysqli_connect_error()."</p>";
		}

		$query = "SELECT * FROM $sql_table";
		$result = mysqli_query($conn, $query);
		if($result) {
			$create_table_query = "CREATE TABLE $sql_table(
                $userID INT NOT NULL UNIQUE,
                $username VARCHAR (30) NOT NULL PRIMARY KEY,
                $passwordAdmin VARCHAR (60) NOT NULL)";
			$result = mysqli_query($conn, $create_table_query);
		}
        handleLogin($conn, $sql_table, $usernameAdmin);
    }
	?>
	<form method="POST" action="loginAdmin.php" class="login">
		<fieldset>
			<?php if(!empty($errorHandler)) 
			{ 
				echo "<p class='error'>$errorHandler</p>";
			} 
			?>
			<legend>Supervisor Login</legend>
			<label for="usernameAdmin">@</label>
			<input type="text" name="usernameAdmin" id="usernameAdmin" placeholder="Admin ID"/><br/>
			<label for="passwordAdmin">🔒</label>
			<input type="password" name="passwordAdmin" id="passwordAdmin" placeholder="Password"/><br/>
			<input type="submit"/>
			<br/>
		</fieldset>
	</form>
	</article>

    <!--Footer-->
    <?php include_once 'footer.inc'; ?>
</body>
</html>