<?php
// index.php
require_once __DIR__ . '/controllers/SeekerController.php';
$action = trim($_GET['action'] ?? 'login');
$controller = new SeekerController();
$controller->dispatch($action);
