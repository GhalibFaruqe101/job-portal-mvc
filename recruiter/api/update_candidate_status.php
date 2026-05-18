<?php
/**
 * AJAX endpoint: update application status
 * Expects POST: application_id, status
 * Returns JSON
 */
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/CandidateModel.php';

header('Content-Type: application/json');   

$model  = new CandidateModel($conn);
$app_id = (int)($_POST['application_id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$app_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit();
}

if ($model->updateStatus($app_id, $status)) {
    echo json_encode(['success' => true, 'message' => 'Status updated.', 'new_status' => $status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed.']);
}
?>
