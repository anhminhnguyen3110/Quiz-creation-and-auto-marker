<?php
    session_start();
    session_unset();
    session_destroy();
    setcookie("ADMIN", "", time()-1000);
    header("Location:index.php");
?>