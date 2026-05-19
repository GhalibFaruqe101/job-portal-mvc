<?php
require_once __DIR__ . '/../helpers/session.php';

// Empty the session array
$_SESSION = array();

// If session cookie was used, expire it in client browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session data on server
session_destroy();

// Redirect back to secure login page
header("Location: AuthController.php?action=login");
exit();
