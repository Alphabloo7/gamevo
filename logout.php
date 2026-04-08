<?php
/**
 * GAMEVO - Logout Page
 */
require_once 'includes/auth.php';

// Require login
requireLogin();

// Logout user
logoutUser();

// Redirect to home page
header("Location: index.php");
exit();
?>
