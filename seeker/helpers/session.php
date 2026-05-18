<?php
// Seeker Module: Session & RBAC Helper
// 1. Start the session safely (prevents "Headers Already Sent" errors)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. Standard Login Check
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}
// 3. Role-Based Access Control (RBAC)
function require_role($required_role) {
    if (!is_logged_in()) {
        header("Location: ../views/login.php");
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        die("Access Denied: You do not have permission to view this page.");
    }
}
?>
