<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
	<h1>Login</h1>
	<?php
	session_start();
	if(isset($_SESSION['StudentID'])){
		header('location: quiz.php');
	}
	$errorHandler = "";
	function sanitise_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
	function studentid_validate($student_id){
		$errMsg = "";
		if ($student_id =="") {
			$errMsg = $errMsg. "<p>You must enter a student id</p>";
		} else if (!preg_match("/^\d{7,10}$/", $student_id)) {
			$errMsg = $errMsg. "<p>Only 7 or 10 digits allowed in your student id.</p>";
		}
		return $errMsg;
	}
	function handleLogin($conn, $sql_table, $studentID, $passwordStudent){
        $username = (int)sanitise_input($_POST['usernameLogin']);
        $password = sanitise_input($_POST['passwordLogin']);
        if(studentid_validate($username)){
			$GLOBALS['errorHandler'] = studentid_validate($username);
			return;
		}
		if(empty($password)){
			$GLOBALS['errorHandler'] = "Invalid password";
			return;
		}
		if(strlen($password)<8){
			$GLOBALS['errorHandler'] = "Password must have more than 8 characters";
			return;
		}
		$usernameSQuery = "SELECT * FROM $sql_table WHERE $studentID = $username LIMIT 1";
		$result = mysqli_query($conn, $usernameSQuery);
		$res = mysqli_fetch_assoc($result);
		if(!$res){
			$GLOBALS['errorHandler'] = "No student ID is provided";
			return;
		}
		if($res['PASSWORD'] != $password){
			$GLOBALS['errorHandler'] = "Bad Credential!";
			return;
		}
		$_SESSION["StudentID"] = $username;
		$_SESSION["firstname"] = $res["FIRST_NAME"];
		$_SESSION["lastname"] = $res["LAST_NAME"];
		header('location: quiz.php');
    }
	if(isset($_POST['usernameLogin']) || isset($_POST['passwordLogin']) ){
		$errorHandler = "";
		$servername = "feenix-mariadb.swin.edu.au";
		$username = "s103515617";
		$password = "reactjs";
		$dbname = "s103515617_db";
		$sql_table = "students";
		$passwordStudent = "PASSWORD";
		$studentID = "STUDENT_ID";
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
				$studentID INT NOT NULL UNIQUE,
				$passwordStudent VARCHAR (60) NOT NULL,
				PRIMARY KEY($studentID)
			)";
			$result = mysqli_query($conn, $create_table_query);
		}
        handleLogin($conn, $sql_table, $studentID, $passwordStudent);
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
			<label for="usernameL">StudentID: </label>
			<input name="usernameLogin" id="usernameL"/><br/>
			<label for="passwordL">Password: </label>
			<input type="password" name="passwordLogin" id="passwordL"/><br/>
			<input type="submit"/>
			<br/>
			<p>Haven't had an account? <a href="register.php"> Register here</a></p>
		</fieldset>
	</form>

</body>
</html>