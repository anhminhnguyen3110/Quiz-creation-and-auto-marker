<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="COS10026 Assignment 2" />
    <meta name="keywords" content="HTML, CSS, JavaScript" />
    <meta name="author" content="React Lions" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/style.css"/>
    <title>Servers Enhancements</title>
</head>
<body>
    <!--Header(with menu)-->
    <?php 
        include ("header.inc");
        include ("menu.inc");
        echo menu("phpEnhancement");
        echo "</header>"
        ?>

    <!--Main-->
    <main id="enhancement">
        <?php 
            echo "<div id='gap'>

                </div>";
        ?>

        <?php
            echo "<div id='logo-enhance'>";
            $alpha = array('','a','b','c');
            for ($i=0; $i<4; $i++) {
                $z = $i + 1;
                echo "<div class='icon' id='i$z'>";
                if ($i>=1) {
                    for ($j=0; $j<$i; $j++) {
                        $k = $j+1;
                        echo "<div class='elip' id = '{$alpha[$i]}eli$k'></div>";
                    }
                }
                echo "<div class='ball'></div>";
                echo "</div>";
            }
            echo "</div>";
        ?>
        
        <?php
            echo "<section class='item'>
                    <h2>Student login and register system</h2>
                    <p>The goal of this system is to store the firstname, lastname and student_id of student in a new table called student that is required so with a student id, there can be only one consistent firstname and lastname.
                    This also includes other enhancement, that is normalising the attempt table and create a primary key for that, foreign key for students table and link them together</p>
                    <p>There is a site for register so student can register their information to start doing the quiz, as well as a login, link here <a href='logoutEnhancements.php'>login & register</a></p>
                </section>";

            echo "<section class='item'>
                <h2>Admin login and security handler</h2>
                <p>The goal of this enhancement is to create a table storing username and password to access manage.php, if an user wrongly type password three times, the access to that account will be blocked for 5 miniutes until it can be log in again.</p>
                <p>There is a site for supervisor login to access manage.php, link here <a href='logoutAminEnhancements.php'>Supervisor login</a></p>
            </section>";

            echo "<section class='item'>
                <h2>Admin sort option</h2>
                <p>This enhancement allows admin to sort the table according to criteria such as score, attemptId, firstname and lastname, which make it more easier to arrange data.</p>
                <p><a href='./manage.php#sortManage'>Table sorting feature</a></p>
            </section>";

        ?>
    </main>
    
    <!--Footer-->
    <?php include_once 'footer.inc'; ?>
</body>
</html>