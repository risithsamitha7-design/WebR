<?php
// GlobeTrek Adventures Logout Handler

// Start session to access it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Destroy session cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start a fresh session to hold the logout success flash message
session_start();
$_SESSION['flash_success'] = "You have successfully logged out. Have a wonderful day!";

// Redirect back to homepage
header("Location: index.php");
exit();
?>
