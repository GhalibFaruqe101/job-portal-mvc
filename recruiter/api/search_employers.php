<?php
/**
 * AJAX endpoint: search employer accounts for client linking
 * Expects GET: q (search query)
 * Returns JSON array of matching employers
 */
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ClientModel.php';

header('Content-Type: application/json');

$model = new ClientModel($conn);
$query = trim($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

$results = $model->searchEmployers($query);
echo json_encode($results);
?>
