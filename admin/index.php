<?php

require_once __DIR__ . '/controllers/AdminController.php';

$action = $_GET['action'] ?? 'dashboard';
$controller = new AdminController();
$controller->dispatch($action);
?>
