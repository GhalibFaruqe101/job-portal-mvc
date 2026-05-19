<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../config/db.php';

// Simple router based on query param 'action'
$action = $_GET['action'] ?? 'login';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepared statement to fetch employer user
    $stmt = $conn->prepare(
        'SELECT id, password_hash 
         FROM users 
         WHERE email = ? AND role = "employer"'
    );

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = 'employer';

            header('Location: ../views/dashboard.php');
            exit();
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $error = 'No employer account found with that email.';
    }
}


// Render requested view
switch ($action) {
    case 'login':
    default:
        require '../views/login.php';
        break;
}