<?php
/**
 * GAMEVO - Admin Logout
 */
require_once 'includes/admin_auth.php';

// Require admin login
requireAdminLogin();

// Logout admin
logoutAdmin();

// Redirect to home
header("Location: index.php");
exit();
?>
