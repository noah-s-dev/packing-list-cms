<?php
/**
 * User Logout Page
 */

require_once 'includes/auth.php';

// Logout user
logoutUser();

// Redirect to login page with success message
header('Location: login.php?message=logged_out');
exit();
?>

