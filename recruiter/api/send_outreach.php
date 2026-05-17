<?php
/**
 * AJAX endpoint: send outreach
 */
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/OutreachModel.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit();
}

$recruiter_id = $_SESSION['user_id'];
$seeker_id    = (int)($_POST['seeker_id'] ?? 0);
$job_id       = (int)($_POST['job_id'] ?? 0);
$message      = trim($_POST['message'] ?? '');

if (!$seeker_id || !$job_id || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit();
}

$model = new OutreachModel($conn);
$result = $model->sendOutreach($recruiter_id, $seeker_id, $job_id, $message);

if ($result === -1) {
    echo json_encode(['success' => false, 'error' => 'You have recently contacted this candidate for this job.']);
} elseif ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send outreach. Please try again.']);
}
?>
