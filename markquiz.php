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
    <title>MarkQuiz</title>
</head>
<body>
    <?php 
     // Create connection
    $servername = "feenix-mariadb.swin.edu.au";
    $username = "s103515617";
    $password = "reactjs";
    $dbname = "s103515617_db";

    $conn = @mysqli_connect(
            $servername, 
            $username, 
            $password, 
            $dbname
    );
    // Check connection
    if (!$conn) {
        echo "<p>Connection failed: " . mysqli_connect_error()."</p>";
    } 
    // Successful connection
    else {
        //check table, exists
        $sql_table = "attempts";
        $query = "SELECT * FROM $sql_table";
        $result = mysqli_query($conn, $query);
        
        // set variable
        $ATTEMPT_ID = "ATTEMPT_ID";
        $STUDENT_ID = "STUDENT_ID";
        $FIRST_NAME = "FIRST_NAME";
        $LAST_NAME = "LAST_NAME";
        $SCORE = "SCORE";
        $CREATED_AT = "CREATED_AT";
        $ATTEMPT_NUMBER = "ATTEMPT_NUMBER";
        
        // Create table if not exists
        if (!$result) {
            $create_table_query = "CREATE TABLE attempts(
                $ATTEMPT_ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                $STUDENT_ID INTEGER NOT NULL,
                $FIRST_NAME VARCHAR(30) NOT NULL,
                $LAST_NAME VARCHAR (30) NOT NULL,
                $SCORE INTEGER NOT NULL,
                $CREATED_AT DATETIME NOT NULL,
                $ATTEMPT_NUMBER INTEGER NOT NULL
            );";
            $result = mysqli_query($conn, $create_table_query);
            // free up the memory
            mysqli_free_result($result);
        }
    
        // Display the retrieved records 
    

        // Question check

        
        if (isset ($_POST["studentid"])) { 
            $student_id = $_POST["studentid"];
        } else {
            header ("Location: quiz.php");
        };
        if (isset ($_POST["firstname"])) $firstname = $_POST["firstname"];
        if (isset ($_POST["lastname"])) $lastname = $_POST["lastname"];
        if (isset ($_POST["q1"])) $q1 = $_POST["q1"];
        if (isset ($_POST["q2"])) $q2 = $_POST["q2"];
        if (isset ($_POST["q3"])) $q3 = $_POST["q3"];
        if (isset ($_POST["q4"])) $q4 = $_POST["q4"];
        if (isset ($_POST["q5"])) $q5 = $_POST["q5"];
        if (isset ($_POST["q6"])) $q6 = $_POST["q6"];
        $new_q4 = implode(', ', $q4);
        $errMsg = "";
        // if ($firstname==""){
        //     $errMsg .= "<p>You must enter your first name. </p>";
        // }
        if (!preg_match("/[a-zA-Z- ]{1,30}/", $firstname)){
            $errMsg .= "<p>Only alpha, space, hyphen characters allowed in your first name. </p>";
        }
        // else if ($lastname==""){
        //     $errMsg .= "<p>You must enter your last name. </p>";
        // }
        else if(!preg_match("/[a-zA-Z- ]{1,30}/", $lastname)){
            $errMsg .= "<p>Only alpha, space, hyphen characters allowed in your last name. </p>";
        }
        else if(!preg_match("/^\d{7,10}$/", $student_id)){
            $errMsg .= "<p>Only 7 or 10 digits allowed in your student id. </p>";
        }
        else if ($q1==""){
            $errMsg .= "<p>You must answer question 1. </p>";}
        else if ($q2==""){
            $errMsg .= "<p>You must answer question 2. </p>";}
        else if ($q3==""){
            $errMsg .= "<p>You must answer question 3. </p>";}
        else if ($new_q4 == ""){
            $errMsg .= "<p>You must answer question 4. </p>";}
        else if ($q5==""){
            $errMsg .= "<p>You must answer question 5. </p>";}
        else if ($q6==""){
            $errMsg .= "<p>You must answer question 6. </p>";}

        if ($errMsg != "") echo "<p>$errMsg</p>";
        else{
        // Sanitise input
        function sanitise_input($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        $firstname = sanitise_input($firstname);
        $lastname = sanitise_input($lastname);
        $q1 = sanitise_input($q1);
        $q2 = sanitise_input($q2);
        $q3 = sanitise_input($q3);
        $new_q4 = sanitise_input($new_q4);
        $q5 = sanitise_input($q5);
        $q6 = sanitise_input($q6);
        // multi-select check
        $q4Answers = ["Open Source", "A framework for building web and mobile applications", "Flexible to use and Good for SEO (Search Engine Optimization)"];
        $q4mark = 0;
        // foreach ($q4Answers as $answer) {
        //     if (in_array($answer, $q4)) {
        //         $q4mark += 1;
        //     }
        // }
        // $q4mark = $q4mark/3;
        if ($q4 == $q4Answers)
            $q4mark = 1;
        else
            $q4mark = 0;

        // Quiz Marking
        $mark = 0;
        if ($q1 == "JS") {
            $mark += 1;
        }
        if ($q2 == "Jordan Walke") {
            $mark += 1;
        }
        if ($q3 == "facebook") {
            $mark += 1;
        }
        if ($q4mark) {
            $mark += $q4mark;
        }
        if ($q5 == 2016) {
            $mark += 1;
        }
        if ($q6 == "false") {
            $mark += 1;
        }
        
        $score = intval($mark / 6 * 100);
        // $new_q4 = implode(', ', $q4);

        
            date_default_timezone_set('Australia/Melbourne');
            $formatted_time = date("Y-m-d H:i:s", time());

            // Check attempts left by Student ID
            $attempt = 1;
            $attempts_left_query = "SELECT $ATTEMPT_NUMBER FROM attempts WHERE $STUDENT_ID = $student_id";
            $data = mysqli_query($conn, $attempts_left_query);
            if (!$data) {
                echo "Error: " . $attempts_left_query . "<br>" . mysqli_error($conn);
            } else if (mysqli_num_rows($data) == 0) {
                $attempt = 2;
            } else {
                $attempts_left = 0;
                while($row = mysqli_fetch_assoc($data)) {
                    $attempts_left = 2 - $row[$ATTEMPT_NUMBER];
                }
                if ($attempts_left > 0) {
                    // Insert into table
                    $insert_query = "UPDATE attempts SET $ATTEMPT_NUMBER = 2 WHERE $STUDENT_ID = $student_id";
                    $insert_query = "INSERT INTO attempts ($STUDENT_ID, $FIRST_NAME, $LAST_NAME, SCORE, $CREATED_AT, $ATTEMPT_NUMBER) 
                VALUES ($student_id, '$firstname',  '$lastname', $score, '$formatted_time', 2);";
                    $result = mysqli_query($conn, $insert_query);
                    $attempt = 0;
                     
                    echo "<h1>MarkQuiz</h1>";
                    echo "<h2>Student ID: $student_id</h2>";
                    echo "<h2>Name: $firstname $lastname</h2>";
                    echo "<h2>Answer 1: $q1</h2>";
                    echo "<h2>Answer 2: $q2</h2>";
                    echo "<h2>Answer 3: $q3</h2>";
                    echo "<h2>Answer 4: $new_q4</h2>";
                    echo "<h2>Answer 5: $q5</h2>";
                    echo "<h2>Answer 6: $q6</h2>";
                    echo "<h2>Score: $score</h2>";
                    echo "Attempts Left: $attempt";
                } else {
                    echo "<p>You have no attempts left</p>";
                    $attempt = 0;
                }
            }
            if ($attempt > 0) {
                
                echo "<h1>MarkQuiz</h1>";
                echo "<h2>Student ID: $student_id</h2>";
                echo "<h2>Name: $firstname $lastname</h2>";
                echo "<h2>Answer 1: $q1</h2>";
                echo "<h2>Answer 2: $q2</h2>";
                echo "<h2>Answer 3: $q3</h2>";
                echo "<h2>Answer 4: $new_q4</h2>";
                echo "<h2>Answer 5: $q5</h2>";
                echo "<h2>Answer 6: $q6</h2>";
                echo "<h2>Score: $score</h2>";

                // Insert into table
                $sql = "INSERT INTO attempts ($STUDENT_ID, $FIRST_NAME, $LAST_NAME, $SCORE, $CREATED_AT, $ATTEMPT_NUMBER) 
                VALUES ($student_id, '$firstname',  '$lastname', $score, '$formatted_time', 1);";
                
                if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
                } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }

            }
        }
            
            mysqli_close($conn);
        }
    ?>    
</body>
</html>