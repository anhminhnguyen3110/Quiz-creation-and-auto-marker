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

<body class="quiz-bg">
    <!--Header(with menu)-->
    <?php 
        include ("header.inc");
        include ("menu.inc");
        echo menu("quiz");
        echo "</header>"
    ?>
    <article class='quiz manager'>
    <h1 class='heading-quiz'>Admin site</h1>
    <form method="get" action="manage.php">
        <fieldset class="need">
            <legend>All attempt for student</legend>
            <label for="Student-ID">Name or ID: </label><br/>
            <input id="Student-ID" name="all" type="text"/><br/>
            <input type="submit" value="Submit"/>
        <br />
        </fieldset>
    </form>
    <form method="post" action="manage.php">
        <fieldset class="need">
            <legend>Delete all attempts for a Student</legend>
            <label for="delete">Student ID: </label><br/>
            <input id="delete" name="deleteID" type="text"/><br/>
            <input type="submit" value="Submit"/>
        </fieldset>
    </form>
    <form method="post" action="manage.php">
        <fieldset class="need">
            <legend>Change the score for a quiz attempt</legend>
            <label for="changeID">Student ID: </label><br/>
            <input id="changeID" name="changeID" type="text"/><br/>
            <label for="Attempt">Attempt: </label>
            <select name="changeAttempt" id="Attempt">
                <option value="1">1</option>
                <option value="2">2</option>
            </select><br/>
            <label for="changeScore">Score: </label><br/>
            <input id="changeScore" name="changeScore" type="text"/><br/>
            <input type="submit" value="Submit"/>
        </fieldset>
    </form>
    <?php
    session_start();
	if(!isset($_COOKIE['ADMIN'])){
		header('location: loginAdmin.php');
	}
    $servername = "feenix-mariadb.swin.edu.au";
    $username = "s103515617";
    $password = "reactjs";
    $dbname = "s103515617_db";

    $AttemptID = "ATTEMPT_ID";
    $studentID = "STUDENT_ID";
    $firstname = "FIRST_NAME";
    $lastname = "LAST_NAME";
    $score = "SCORE";
    $create_at = "CREATED_AT";
    $attempt_number = "ATTEMPT_NUMBER";
    function displayTable($result){
        echo "<table class='managetb'>\n";
        echo "<tr>"
        ."<th scope='col'>", $GLOBALS['AttemptID'], "</th>"
        ."<th scope='col'>", $GLOBALS['studentID'], "</th>"
        ."<th scope='col'>", $GLOBALS['firstname'], "</th>"
        ."<th scope='col'>", $GLOBALS['lastname'], "</th>"
        ."<th scope='col'>", $GLOBALS['score'], "</th>"
        ."<th scope='col'>", $GLOBALS['create_at'], "</th>"
        ."<th scope='col'>", $GLOBALS['attempt_number'], "</th>"
        ."</tr>";
        while($row = mysqli_fetch_assoc($result)){
            echo "<tr>"
            ."<td>", $row[$GLOBALS['AttemptID']], "</td>"
            ."<td>", $row[$GLOBALS['studentID']], "</td>"
            ."<td>", $row[$GLOBALS['firstname']], "</td>"
            ."<td>", $row[$GLOBALS['lastname']], "</td>"
            ."<td>", $row[$GLOBALS['score']], "</td>"
            ."<td>", $row[$GLOBALS['create_at']], "</td>"
            ."<td>", $row[$GLOBALS['attempt_number']], "</td>"
            ."</tr>";
        }
        echo "</table>";
    }
    function displayTableStudent($result){
        echo "<table>\n";
        echo "<tr>"
        ."<th scope='col'>", $GLOBALS['studentID'], "</th>"
        ."<th scope='col'>", $GLOBALS['firstname'], "</th>"
        ."<th scope='col'>", $GLOBALS['lastname'], "</th>";
        while($row = mysqli_fetch_assoc($result)){
            echo "<tr>"
            ."<td>", $row[$GLOBALS['studentID']], "</td>"
            ."<td>", $row[$GLOBALS['firstname']], "</td>"
            ."<td>", $row[$GLOBALS['lastname']], "</td>"
            ."</tr>";
        }
        echo "</table>";
    }
    function sanitise_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    function displayTableStudentSearch($result){
        $row = mysqli_fetch_all($result);
        if(!sizeof($row)){
            echo "<p>No student is found</p>";
            return;
        }
        echo "<table>\n";
        echo "<tr>"
        ."<th scope='col'>", $GLOBALS['studentID'], "</th>"
        ."<th scope='col'>", $GLOBALS['firstname'], "</th>"
        ."<th scope='col'>", $GLOBALS['lastname'], "</th>"
        ."<th scope='col'>", $GLOBALS['score'], "</th>"
        ."<th scope='col'>", $GLOBALS['create_at'], "</th>"
        ."<th scope='col'>", $GLOBALS['attempt_number'], "</th>";
        $i = 0;
        while($i < sizeof($row)){
            echo "<tr>"
            ."<td>", $row[$i][0], "</td>"
            ."<td>", $row[$i][1], "</td>"
            ."<td>", $row[$i][2], "</td>"
            ."<td>", $row[$i][3], "</td>"
            ."<td>", $row[$i][4], "</td>"
            ."<td>", $row[$i][5], "</td>"
            ."</tr>";
            $i +=1;
        }
        echo "</table>";
    }





    function changeAttemptHandler($conn, $sql_table, $score, $studentID, $attempt_number){
       $id = sanitise_input($_POST['changeID']);
       $attempt = sanitise_input($_POST['changeAttempt']);  
       $newScore = sanitise_input($_POST['changeScore']);
       if($newScore > 100 || $newScore < 0 || is_int($newScore)){
            echo "Invalid input, please try again";
            return;
       }
       $searchQuery = "SELECT * FROM $sql_table WHERE $studentID = $id AND $attempt_number = $attempt";
       $resultSearch = mysqli_query($conn, $searchQuery);
       $check = mysqli_fetch_all($resultSearch);
       if(!sizeof($check)){
        mysqli_free_result($resultSearch);
        echo "<p>No student is found</p>";
        return;
       }
       $queryUpdate = "UPDATE $sql_table SET $score = $newScore WHERE $studentID = $id AND $attempt_number=$attempt ";
       $resultUpdate = mysqli_query($conn, $queryUpdate);
       $queryUpdateShow = "SELECT * FROM $sql_table WHERE $studentID = $id";
       $resultUpdateShow = mysqli_query($conn, $queryUpdateShow);
       displayTable($resultUpdateShow);
       mysqli_free_result($resultSearch);
       mysqli_free_result($resultUpdateShow);
    }


    function deleteHandler($conn ,$sql_table, $studentID){
        $delete = sanitise_input($_POST['deleteID']);
        $searchQuery = "SELECT * FROM $sql_table WHERE $studentID = $delete";
        $resultSearch = mysqli_query($conn, $searchQuery);
        $check = mysqli_fetch_all($resultSearch);
        if(!sizeof($check)){
            mysqli_free_result($resultSearch);
            echo "<p>No student is found</p>";
            return;
        }
        $queryDelete = "DELETE FROM $sql_table WHERE $studentID = $delete";
        $resultDelete = mysqli_query($conn, $queryDelete);
        mysqli_free_result($resultSearch);
        echo "<h2>Delete successfully</h2>";
    }

    function allHandler($conn, $sql_table,$firstname, $lastname, $studentID,$score,$create_at, $attempt_number){
        $search = sanitise_input($_GET['all']);
        $querySearch = "SELECT $firstname, $lastname,$studentID,$score, $create_at, $attempt_number FROM $sql_table
        WHERE CONCAT($firstname,' ',$lastname) = '$search' OR $firstname LIKE '$search%'
        OR $lastname LIKE '$search%' OR $studentID = '$search%'";
        $resultSearch = mysqli_query($conn, $querySearch);
        $count = mysqli_fetch_lengths($resultSearch);
        echo "<h1>$count</h1>";
        displayTableStudentSearch($resultSearch);
        mysqli_free_result($resultSearch);
    }


    try {
        $conn = mysqli_connect($servername, $username, $password, $dbname);
    } catch (\Throwable $th) {
        echo "<p>Connection failed: " . mysqli_connect_error()."</p>";
    }
    // form hanlder
    $sql_table = 'attempts';
    if(isset($_GET['all'])){
       allHandler($conn, $sql_table,$firstname, $lastname, $studentID,$score,$create_at, $attempt_number);
    }else if(isset($_POST['deleteID'])){
        deleteHandler($conn ,$sql_table, $studentID);
    }else if(isset($_POST['changeID']) && isset($_POST['changeAttempt']) && isset($_POST['changeScore'])){
        changeAttemptHandler($conn, $sql_table, $score, $studentID,  $attempt_number);
    }

    
    
    // All attempts
    echo "<h2>All quiz attempts</h2>";
    echo '<form method="get" action="">
    <fieldset>
    <legend>Sort by</legend>
            <label for="sort">Column</label>
            <select id="sort" name="sortColumn">
                <option value="ATTEMPT_ID">Attempt ID</option>
                <option value="FIRST_NAME">First name</option>
                <option value="LAST_NAME">Last name</option>
                <option value="SCORE">Score</option>
            </select>
            <label for="sortDirection">Sort direction</label>
            <select id="sortDirection" name="sortDirection">
                <option value="ASC">Arrange from smallest to the largest value</option>
                <option value="DESC">Arrange from largest to the smallest value</option>
            </select>
            <input type="submit"/>
        </fieldset>
    </form>';
    $query = "SELECT * FROM $sql_table";
    if(isset($_GET['sortColumn']) || isset($GET['sortDirection'])){
        $sortColumn = $_GET['sortColumn'];
        $sortDirection = strtoupper($_GET['sortDirection']);
        $query = $query." ORDER BY $sortColumn $sortDirection";
        echo $query;
    }
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $create_table_query = "CREATE TABLE attempts(
            ATTEMPT_ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            STUDENT_ID INTEGER,
            FIRST_NAME VARCHAR(30),
            LAST_NAME VARCHAR (30),
            SCORE INTEGER,
            ATTEMPT_TIME DATETIME,
            ATTEMPT_NUM INTEGER
        );";
        $result = mysqli_query($conn, $create_table_query);
    }
    displayTable($result);
    // Who got 100%
    echo "<h2>All students who got 100% on their first attempt</h2>";
    $query = "SELECT $studentID, $firstname, $lastname  FROM $sql_table WHERE $attempt_number = 1 AND $score = 100";
    $result = mysqli_query($conn, $query);
    displayTableStudent($result);

    //Who got less than 50 on second attempt
    echo "<h2>All students who got less than 50% on their second attempt</h2>";
    $query = "SELECT $studentID, $firstname, $lastname  FROM $sql_table WHERE $attempt_number = 2 AND $score < 50";
    $result = mysqli_query($conn, $query);
    displayTableStudent($result);
    



    mysqli_free_result($result);
    mysqli_close($conn);
    ?>
    </article>
    <!--Footer-->
    <?php include_once 'footer.inc'; ?>
</body>
</html>