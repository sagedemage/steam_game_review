<?php
//start or resume the session
session_start();

//check if the user is logged in (a session variable is set)
if (isset($_SESSION['username'])) {
    //unset all of the session variables
    $_SESSION = array();

    //Destroy the session
    session_destroy();
}

//Redirect the user to the homepafe after logging out
header("Location: home.html");
exit;
?>