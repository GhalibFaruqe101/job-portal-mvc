<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

$action = $_GET['action'] ?? 'show';

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $company = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password_hash'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? "");


    $industry = '';
    $size = '';
    $description = '';
    $website = '';
    $address = '';
    $name = '';
    $role = 'employer';

    // Validation
    if ($company && $email && $password) {

        // Hash password
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert into users table
        $stmt = $conn->prepare(
            'INSERT INTO users
            (name, email, password_hash, role, phone)
            VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->bind_param('sssss', $company, $email, $hash, $role, $phone);

        if ($stmt->execute()) {

            // Get inserted user ID
            $userId = $stmt->insert_id;

            $stmt->close();

            // Insert employer profile
            $stmt = $conn->prepare(
                'INSERT INTO employer_profiles
                (user_id, company_name, industry, company_size, description, website, address)
                VALUES (?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->bind_param(
                'issssss',
                $userId,
                $company,
                $industry,
                $size,
                $description,
                $website,
                $address
            );

            if ($stmt->execute()) {

                $stmt->close();

                $success = 'Registration submitted successfully.';
                require '../views/register_success.php';
                exit();

            } else {

                $error = 'Failed to create employer profile: ' . $stmt->error;
            }

        } else {

            $error = 'Registration failed: ' . $stmt->error;
        }

    } else {

        $error = 'All required fields must be filled.';
    }
}

// Show registration form
require '../views/register.php';
?>