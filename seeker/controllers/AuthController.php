<?php
// Seeker Module: Authentication Controller
session_start();
require_once '../config/db.php';
require_once '../models/UserModel.php';

$userModel = new UserModel($conn);
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        handleLogin($userModel);
        break;
    case 'register':
        handleRegister($userModel);
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        header("Location: ../views/login.php");
        exit();
}

function handleLogin($userModel) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/login.php");
        exit();
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    }

    if (empty($errors)) {
        $user = $userModel->findByEmail($email);

        if ($user && $user['role'] === 'seeker' && $userModel->verifyPassword($password, $user['password_hash'])) {
            if (!$user['is_active']) {
                $errors[] = "Your account has been deactivated. Contact support.";
            } else {
                // Set the mandatory session keys
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = 'seeker';
                $_SESSION['user_name'] = $user['name'];
                header("Location: ../views/dashboard.php");
                exit();
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }

    // Store errors in session and redirect back
    $_SESSION['auth_errors'] = $errors;
    $_SESSION['old_email'] = $email;
    header("Location: ../views/login.php");
    exit();
}

function handleRegister($userModel) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/register.php");
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $errors = [];

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "Name, email, and password are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $existing = $userModel->findByEmail($email);
        if ($existing) {
            $errors[] = "An account with this email already exists.";
        }
    }

    if (empty($errors)) {
        if ($userModel->createUser($name, $email, $phone, $password)) {
            $_SESSION['auth_success'] = "Registration successful! Please log in.";
            header("Location: ../views/login.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }

    // Store errors and old input in session
    $_SESSION['auth_errors'] = $errors;
    $_SESSION['old_name'] = $name;
    $_SESSION['old_email'] = $email;
    $_SESSION['old_phone'] = $phone;
    header("Location: ../views/register.php");
    exit();
}

function handleLogout() {
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}
?>
