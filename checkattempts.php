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
    <title>Check attempt</title>
</head>
<body>
    <!--Header(with menu)-->
    <?php 
        include ("header.inc");
        include ("menu.inc");
        echo menu("checkattempts");
        echo "</header>";
        
        if(!isset($_SESSION['StudentID'])){  
            header('location: login.php');
        }
    ?>
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
            echo "<p>Connection failed: " . mysqli_connect_error()."</p>\n";
        } 
        // Successful connection
        else {
                $student_id =  $_SESSION["StudentID"];
                $attempt_query = "SELECT * FROM attempts WHERE ATTEMPT_NUMBER = 2 AND STUDENT_ID = $student_id";
                $result = mysqli_query($conn, $attempt_query);
                //checking if maximum attempts have been reached
                if ($result->num_rows == 0) {
                    header('location: quiz.php');
                } else {
                    echo "
                    <main id='topic-main'>
                    <section id='attempts'>
                    <h2>You have reached the maximum number of attempts for the Quiz.</h2>
                    <p>You can not attempt the quiz again:</p>
                        <ul>
                            <li>Explore the <a href='index.php'>website</a>!</li>
                            <li>View your progress <a href='status.php'>status</a> for attempts!</li>
                        </ul>
                    </section>
                    </main>";
                }
            }
    ?>
    <?php include_once 'footer.inc'; ?>
</body>
</html>