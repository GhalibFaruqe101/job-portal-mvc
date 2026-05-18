<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser caching for all secure pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

function require_employer_login()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'employer') {
        header('Location: ../controllers/AuthController.php?action=login');
        exit();
    }
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
        header("Location: ../controllers/AuthController.php?action=login");
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        die("Access Denied: You do not have permission to view this page.");
    }
}
?>