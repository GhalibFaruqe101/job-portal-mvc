<?php
// Recruiter Module: Candidate Controller
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/CandidateModel.php';

$model  = new CandidateModel($conn);
$action = $_GET['action'] ?? '';

if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_id = (int)($_POST['application_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    $recruiter_id = $_SESSION['user_id'];
    if ($app_id && $model->updateStatus($app_id, $status, $recruiter_id)) {
        $_SESSION['candidate_success'] = "Candidate status updated successfully.";
    } else {
        $_SESSION['candidate_error'] = "Failed to update status.";
    }
    header("Location: ../views/candidates.php");
    exit();
}

header("Location: ../views/candidates.php");
exit();
?>
