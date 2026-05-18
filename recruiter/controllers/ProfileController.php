<?php
// Recruiter Module: Profile Controller
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ProfileModel.php';

$profileModel = new ProfileModel($conn);
$action = $_GET['action'] ?? 'update';

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['profile_errors'] = ["Invalid security token. Please try again."];
        header("Location: ../views/profile.php");
        exit();
    }

    $user_id      = $_SESSION['user_id'];
    $name         = trim($_POST['name'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $agency_name  = trim($_POST['agency_name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $website      = trim($_POST['website'] ?? '');
    $errors       = [];

    if (empty($name) || empty($agency_name)) {
        $errors[] = "Full name and agency name are required.";
    }

    // Handle profile picture upload
    $pic_path = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['profile_pic']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $errors[] = "Only JPG, PNG, GIF, or WEBP images are allowed.";
        } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Profile picture must be under 2MB.";
        } else {
            $upload_dir = '../../uploads/profile_pics/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = 'recruiter_' . $user_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $filename)) {
                $pic_path = 'uploads/profile_pics/' . $filename;
            } else {
                error_log("move_uploaded_file failed for user $user_id");
                $errors[] = "Failed to upload profile picture. Please try again.";
            }
        }
    }

    if (empty($errors)) {
        $profileModel->updateUser($user_id, $name, $phone);
        $profileModel->updateRecruiterProfile($user_id, $agency_name, $specialization, $description, $website);

        if ($pic_path) {
            $profileModel->updateProfilePic($user_id, $pic_path);
        }

        // Update session name in case it changed
        $_SESSION['user_name'] = $name;
        $_SESSION['profile_success'] = "Profile updated successfully!";
    } else {
        $_SESSION['profile_errors'] = $errors;
    }
}

header("Location: ../views/profile.php");
exit();
?>
