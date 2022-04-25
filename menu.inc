<?php
    function menu($which) {
        $result = "<nav class='navigation'>
                    <!--Checkbox for Hamburger menu in small screens for responsive-->
                    <input type='checkbox' id='checkburger'>
                    <label for='checkburger' class='hamburger'>&#9776;</label>
                    <!--Menu-->
                    <div class='menu'>
                    <ul class='nav-links'>";
        if ($which == "index") {
            $result = $result."<li><a href='index.php' class='this'>Home</a></li>
                <li><a href='topic.php'>Topic</a></li>
                <li><a href='quiz.php'>Quiz</a></li>
                <li><a href='enhancements.html'>Enhanced</a></li>";
        } else if ($which == "topic") {
            $result = $result."<li><a href='index.php'>Home</a></li>
                <li><a href='topic.php' class='this'>Topic</a></li>
                <li><a href='quiz.php'>Quiz</a></li>
                <li><a href='enhancements.html'>Enhanced</a></li>";
        } else if ($which == "quiz") {
            $result = $result."<li><a href='index.php'>Home</a></li>
                <li><a href='topic.php'>Topic</a></li>
                <li><a href='quiz.php' class='this'>Quiz</a></li>
                <li><a href='enhancements.html'>Enhanced</a></li>";
        } else if ($which == "enhancements") {
            $result = $result."<li><a href='index.php'>Home</a></li>
                <li><a href='topic.php'>Topic</a></li>
                <li><a href='quiz.php'>Quiz</a></li>
                <li><a href='enhancements.html' class='this'>Enhanced</a></li>";
        }
        $result = $result."</ul></div></nav>";
        echo $result;
    }
?>