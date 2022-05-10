<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
	<h1>Login for Admin</h1>
	<?php
	session_start();
	if(isset($_SESSION['ADMIN'])){
		header('location: manage.php');
	}
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
		try {
			$result = mysqli_query($conn, $query);
		} catch (\Throwable $th) {
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
			$GLOBALS['errorHandler'] = "Bad Credential!";
			loginSecurityHandler($usernameInput,$username, $conn);
			return;
		}
		$sql_table = 'logSecurity';
		$query = "SELECT * FROM $sql_table WHERE $username = '$usernameInput'";
		$results = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($results);
		if($row){
			if($row[$attemptTime] == 3){
				if($row[$createdAt] - time() >= 300){
	
				}else{
					$GLOBALS['errorHandler'] = "Maximum of attempt to login this account";
					return;
				}
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
		$username = "s103515617";
		$password = "reactjs";
		$dbname = "s103515617_db";
		$sql_table = "admin";


        $userID = "USER_ID";
        $usernameAdmin = "USER_NAME";
		$passwordAdmin = "PASSWORD";
		
		try {
		  $conn = mysqli_connect($servername, $username, $password, $dbname);
		} catch (\Throwable $th) {
			echo "<p>Connection failed: " . mysqli_connect_error()."</p>";
		}

		$query = "SELECT * FROM $sql_table";
		try {
			$result = mysqli_query($conn, $query);
		} catch (\Throwable $th) {
			$create_table_query = "CREATE TABLE $sql_table(
                $userID INT NOT NULL UNIQUE,
                $username VARCHAR (30) NOT NULL PRIMARY KEY,
                $passwordAdmin VARCHAR (60) NOT NULL)";
			$result = mysqli_query($conn, $create_table_query);
		}
        handleLogin($conn, $sql_table, $usernameAdmin);
    }
	?>
	<form method="POST" action="">
		<fieldset>
			<?php if(!empty($errorHandler)) 
			{ 
				echo "<p>$errorHandler</p>";
			} 
			?>
			<legend>Login</legend>
			<label for="usernameAdmin">AdminID: </label>
			<input name="usernameAdmin" id="usernameAdmin"/><br/>
			<label for="passwordAdmin">Password: </label>
			<input type="password" name="passwordAdmin" id="passwordAdmin"/><br/>
			<input type="submit"/>
			<br/>
		</fieldset>
	</form>

</body>
</html>