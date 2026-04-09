<?php
/**
 * GAMEVO - Admin Logout
 */
require_once '../../includes/admin_auth.php';

// Clear cache headers FIRST
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
header("X-UA-Compatible: IE=edge");

// Require admin login first (to verify they're logged in)
requireAdminLogin();

// Logout admin (this will unset and destroy session)
logoutAdmin();

// Additional security headers for logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0", true);
header("Pragma: no-cache", true);
header("Expires: -1", true);

// Redirect to home
header("Location: ../../index.php");
exit();

