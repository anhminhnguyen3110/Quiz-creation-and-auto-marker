<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8" />
    <meta name="description" content="COS10026 Assignment 1" />
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
        include ("header.inc");
        include ("menu.inc");
        echo menu("quiz");
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
	
	function handleLogin($conn, $sql_table, $username){
        $usernameInput = (int)sanitise_input($_POST['usernameAdmin']);
        $passwordInput = sanitise_input($_POST['passwordAdmin']);
        // if(empty($usernameInput)){
		// 	$GLOBALS['errorHandler'] = "Invalid username";
		// 	return;
		// }
		if(empty($passwordInput)){
			$GLOBALS['errorHandler'] = "Invalid password";
			return;
		}
		if(strlen($passwordInput)<8){
			$GLOBALS['errorHandler'] = "Password must have more than 8 characters";
			return;
		}
		$usernameSQuery = "SELECT * FROM $sql_table WHERE $username = $usernameInput LIMIT 1";
		$result = mysqli_query($conn, $usernameSQuery);
		$res = mysqli_fetch_assoc($result);
		if(!$res){
			$GLOBALS['errorHandler'] = "No username is provided";
			return;
		}
		if($res['PASSWORD'] != $passwordInput){
			$GLOBALS['errorHandler'] = "Incorrect password!";
			return;
		}
		$_SESSION["ADMIN"] = $usernameInput;
		// echo $res["FIRST_NAME"];
		// echo $res["LAST_NAME"];
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
	<form method="POST" action="" class="login">
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