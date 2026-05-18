<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function require_role($required_role) {
    if (!is_logged_in()) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if (preg_match('#^(.*?/admin)(?:/.*)?$#', $script, $matches)) {
            $adminRoot = rtrim($matches[1], '/');
        } else {
            $adminRoot = dirname($script);
        }
        header("Location: {$adminRoot}/index.php?action=login");
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        die("Access Denied: You do not have permission to view this page.");
    }
}

function admin_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_admin_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
?>
