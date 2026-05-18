<?php
/**
 * AJAX endpoint: update application status
 * Expects POST: application_id, status, csrf_token
 * Returns JSON
 */
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/CandidateModel.php';

header('Content-Type: application/json');   

// CSRF validation
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
    exit();
}

$model  = new CandidateModel($conn);
$app_id = (int)($_POST['application_id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$app_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit();
}

$recruiter_id = $_SESSION['user_id'];
if ($model->updateStatus($app_id, $status, $recruiter_id)) {
    echo json_encode(['success' => true, 'message' => 'Status updated.', 'new_status' => $status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed.']);
}
?>
