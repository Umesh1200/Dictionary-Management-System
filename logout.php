<?php
// logout.php
require 'includes/auth.php';

// Call the logout function to destroy the session and clear user data
logout();

// Redirect the user to the login page after logging out
header("Location: login.php");
exit;
?>
