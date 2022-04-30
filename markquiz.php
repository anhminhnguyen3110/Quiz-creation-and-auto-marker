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

        // multi-select check
        $q4Answers = ["Open Source", "A framework for building web and mobile applications", "Flexible to use and Good for SEO (Search Engine Optimization)"];
        $q4mark = 0;
        foreach ($q4Answers as $answer) {
            if (in_array($answer, $q4)) {
                $q4mark += 1;
            }
        }
        $q4mark = $q4mark/3;

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
        $new_q4 = implode(', ', $q4);

        $servername = "feenix-mariadb.swin.edu.au";
        $username = "s103515617";
        $password = "150403";
        $dbname = "s103515617_db";

        // Create connection
        $conn = @mysqli_connect(
                $servername, 
                $username, 
                $password, 
                $dbname
        );
        // Check connection
        if (!$conn) {
            echo "<p>Connection failed: " . mysqli_connect_error()."</p>";
        } else {
            //check table, exists
            $sql_table = "attempts";
            $query = "SELECT * FROM $sql_table";
            $result = mysqli_query($conn, $query);

            // Create table if not exists
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

            date_default_timezone_set('Australia/Melbourne');
            $formatted_time = date("Y-m-d H:i:s", time());

            // Check attempts left by Student ID
            $attempt = 1;
            $attempts_left_query = "SELECT ATTEMPT_NUM FROM attempts WHERE STUDENT_ID = $student_id";
            $data = mysqli_query($conn, $attempts_left_query);
            if (!$data) {
                echo "Error: " . $attempts_left_query . "<br>" . mysqli_error($conn);
            } else if (mysqli_num_rows($data) == 0) {
                $attempt = 2;
            } else {
                $attempts_left = 0;
                while($row = mysqli_fetch_assoc($data)) {
                    $attempts_left = 2 - $row['ATTEMPT_NUM'];
                }
                if ($attempts_left > 0) {
                    // Insert into table
                    $insert_query = "UPDATE attempts SET ATTEMPT_TIME = '$formatted_time', ATTEMPT_NUM = 2, SCORE = $score WHERE STUDENT_ID = $student_id";
                    $result = mysqli_query($conn, $insert_query);
                    $attempt = 0;
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
                $sql = "INSERT INTO attempts (STUDENT_ID, FIRST_NAME, LAST_NAME, SCORE, ATTEMPT_TIME, ATTEMPT_NUM) 
                VALUES ($student_id, '$firstname',  '$lastname', $score, '$formatted_time', 1);";

                if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
                } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }

            mysqli_close($conn);
        }
    ?>    
</body>
</html>