<?php

require_once __DIR__ . '/../helpers/session.php';
require_role('admin');
require_once __DIR__ . '/../models/AdminModel.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST request required.']);
    exit;
}
if (!verify_admin_csrf($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Invalid security token.']);
    exit;
}

$userId = (int)($_POST['user_id'] ?? 0);
$action = trim($_POST['account_action'] ?? '');
$reason = trim($_POST['reason'] ?? '');

try {
    $model = new AdminModel();
    $result = $model->updateUserStatus((int)$_SESSION['user_id'], $userId, $action, $reason);
    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
?>
