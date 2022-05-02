<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    	session_start();
        if(isset($_SESSION['StudentID'])){
            header('location: quiz.php');
        }
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
            } else if (!preg_match("/\d{7,10}/", $student_id)) {
                $errMsg = $errMsg. "<p>Only 7 or 10 digits allowed in your student id.</p>";
            }
            return $errMsg;
        }
        function name_validate($name){
            $errMsg = "";
            if ($name =="") {
                $errMsg = $errMsg. "<p>You must enter a name. </p>";
            } else if (!preg_match("/[a-zA-Z- ]{1,30}/", $name)) {
                $errMsg = $errMsg. "<p>Only alpha, space, hyphen characters allowed in your name.</p>";
            }
            return $errMsg;
        }



        function handleRegister($conn, $sql_table, $role,$passwordStudent,$studentID ,$firstname ,$lastname){
            $username = (int)sanitise_input($_POST['usernameRegister']);
            $password = sanitise_input($_POST['passwordRegister']);
            $firstnameRegister = sanitise_input($_POST['firstnameRegister']);
            $lastnameRegister = sanitise_input($_POST['lastnameRegister']);
            echo studentid_validate($username);
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
            if(name_validate($firstnameRegister)){
                $GLOBALS['errorHandler'] = name_validate($firstnameRegister);
                return;
            }
            if(name_validate($lastnameRegister)){
                $GLOBALS['errorHandler'] = name_validate($lastnameRegister);
                return;
            }
            $hashedPassword =  password_hash($password, PASSWORD_BCRYPT);
            $registerQuery = "INSERT INTO $sql_table
            ($studentID, $role, $passwordStudent, $firstname, $lastname)
            VALUES (
                $username,
                'STUDENT',
                '$password',
                '$firstnameRegister',
                '$lastnameRegister'
            )";
            // echo $registerQuery;
            $result = mysqli_query($conn,$registerQuery);
            //verify
            // echo password_verify('http://localhost/register.php', $hashedPassword);
            $_SESSION["StudentID"] = $username;
            $_SESSION["role"] = 'STUDENT';
            $_SESSION["firstname"] = $firstnameRegister;
            $_SESSION["lastname"] = $lastnameRegister;
            echo 'register successfully';
            header('location: quiz.php');
        }
        if(isset($_POST['usernameRegister']) || isset($_POST['passwordRegister'])){
            $errorHandler = "";
            $servername = "feenix-mariadb.swin.edu.au";
            $username = "s103515617";
            $password = "reactjs";
            $dbname = "s103515617_db";
            $firstname = "FIRST_NAME";
            $lastname = "LAST_NAME";
            $sql_table = "students";
            $role = "ROLE";
            $accountID = "ACCOUNT_ID";
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
                    $accountID INT NOT NULL AUTO_INCREMENT,
                    $studentID INT NOT NULL UNIQUE,
                    $role ENUM (\"STUDENT\", \"ADMIN\") NOT NULL,
                    $firstname VARCHAR (60) NOT NULL,
                    $lastname VARCHAR (60) NOT NULL,
                    $passwordStudent VARCHAR (60) NOT NULL,
                    PRIMARY KEY($accountID)
                )";
                $result = mysqli_query($conn, $create_table_query);
            }
            
            handleRegister($conn, $sql_table, $role,$passwordStudent, $studentID,$firstname ,$lastname);
        }
    ?>
	<h1>Register</h1>
	<form method="POST" action="">
		<fieldset>
            <?php if(!empty($errorHandler)) 
                { 
                    echo "<p>$errorHandler</p>";
                } 
			?>
			<legend>Register</legend>
			<label for="usernameR">StudentID:&nbsp; </label>
			<input name="usernameRegister" id="usernameR"/><br/>
			<label for="password">Password:&nbsp;&nbsp; </label>
			<input type="password" name="passwordRegister" id="passwordR"/><br/>
            <label for="firstname">First Name: </label>
			<input type="text" name="firstnameRegister" id="firstnameR"/><br/>
            <label for="lastname">Last Name: </label>
			<input type="text" name="lastnameRegister" id="lastnameR"/><br/>
			<input type="submit"/>
			<p>Already had an account? <a href="login.php">Login here</a></p>
		</fieldset>
	</form>

</body>

</html>