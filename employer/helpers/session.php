<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_employer_login()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'employer') {
        header('Location: ../controllers/AuthController.php?action=login');
        exit();
    }
}
?>