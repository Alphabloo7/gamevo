<?php
/**
 * GAMEVO - Logout Page
 */

// Clear cache headers FIRST
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
header("X-UA-Compatible: IE=edge");

require_once '../../includes/auth.php';

// Require login first (to verify they're logged in)
requireLogin();

// Logout user
logoutUser();

// Additional security headers for logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0", true);
header("Pragma: no-cache", true);
header("Expires: -1", true);

// Redirect to home page
header("Location: ../../index.php");
exit();

