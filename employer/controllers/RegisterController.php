<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

$action = $_GET['action'] ?? 'show';

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = $_POST['company'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $industry = $_POST['industry'] ?? '';
    $size = $_POST['size'] ?? '';
    $description = $_POST['description'] ?? '';
    $website = $_POST['website'] ?? '';
    $address = $_POST['address'] ?? '';

    // Basic validation (can be expanded)
    if ($company && $email && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "employer")');
        $stmt->bind_param('sss', $company, $email, $hash);
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();

        // Insert employer profile, pending admin verification (status column could be added later)
        $stmt = $conn->prepare('INSERT INTO employer_profiles (user_id, company_name, industry, company_size, description, website, address) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssss', $userId, $company, $industry, $size, $description, $website, $address);
        $stmt->execute();
        $stmt->close();

        $success = 'Registration submitted. Await admin approval.';
        require '../views/register_success.php';
        exit();
    } else {
        $error = 'All required fields must be filled.';
    }
}

// Default: show registration form
require '../views/register.php';
?>
