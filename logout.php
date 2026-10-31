<?php
session_start();  // Start the session

// Destroy the session completely
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

// Redirect the user to the login page
header("Location: /login");
exit();  // Stop further script execution after the redirect
?>
