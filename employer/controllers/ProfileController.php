<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'show';
$employer_id = $_SESSION['user_id'];

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $company_size = trim($_POST['company_size'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($company_name) {
        $stmt = $conn->prepare('
            UPDATE employer_profiles 
            SET company_name=?, industry=?, company_size=?, description=?, website=?, address=? 
            WHERE user_id=?
        ');
        $stmt->bind_param('ssssssi', $company_name, $industry, $company_size, $description, $website, $address, $employer_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Company Name is required.";
    }
    
    // Fall through to show the profile again
    $action = 'show';
}

if ($action === 'show') {
    // Fetch current profile data
    $stmt = $conn->prepare('SELECT company_name, industry, company_size, description, website, address FROM employer_profiles WHERE user_id = ?');
    $stmt->bind_param('i', $employer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();

    // Fetch user account info
    $stmt = $conn->prepare('SELECT name, email, phone FROM users WHERE id = ?');
    $stmt->bind_param('i', $employer_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $stmt->close();

    require '../views/profile.php';
}
?>
