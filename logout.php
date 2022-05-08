<?php
    session_start();
    session_unset();
    session_destroy();
    setcookie("STUDENT", "", time()-1000);
    header("Location:login.php");
?>