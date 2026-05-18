<?php
// Recruiter Module: Session & RBAC Helper

// Base path for redirects (works from any include depth)
define('RECRUITER_BASE', '/WT/Project/Project/job-portal-mvc/recruiter');

// 1. Start the session safely (prevents "Headers Already Sent" errors)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. Standard Login Check
function is_logged_in()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}
// 3. Role-Based Access Control (RBAC)
function require_role($required_role)
{
    if (!is_logged_in()) {
        header("Location: " . RECRUITER_BASE . "/views/login.php");
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        http_response_code(403);
        die("Access Denied: You do not have permission to view this page.");
    }
}

// 4. CSRF Protection Helpers
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfInput()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}
?>