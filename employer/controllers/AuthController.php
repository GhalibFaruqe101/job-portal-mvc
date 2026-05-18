<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';


$action = $_GET['action'] ?? 'login';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';


    $stmt = $conn->prepare('SELECT id, password_hash FROM users WHERE email = ? AND role = "employer"');
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
        }
    }
    $error = 'Invalid credentials.';

}


switch ($action) {
    case 'login':
    default:
        require '../views/login.php';
        break;
}
?>