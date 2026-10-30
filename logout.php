<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start fresh session for flash message
session_start();
$_SESSION['flash_message'] = "You have been logged out successfully. Enter your Name, Email ID, and Password to log back in.";

header("Location: login.php");
exit();

