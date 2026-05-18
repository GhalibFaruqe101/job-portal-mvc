<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'index';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic stub for creating a job
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Validate and insert...
    // $employer_id = $_SESSION['user_id'];
    
    // For now, redirect back to dashboard
    header('Location: ../views/dashboard.php');
    exit();
}

switch ($action) {
    case 'create':
        require '../views/create-job.php';
        break;
    case 'index':
    default:
        // Show manage jobs view (to be implemented)
        header('Location: ../views/dashboard.php');
        break;
}
?>
