<?php
// index.php — Single entry point (Front Controller)
// This file auto-detects the base path — works on any machine/folder.

require_once __DIR__ . '/config/app.php';       // sets BASE_URL, BASE_PATH
require_once __DIR__ . '/controllers/SeekerController.php';

$action = trim($_GET['action'] ?? 'login');

$controller = new SeekerController();
$controller->dispatch($action);
