<?php
// index.php — Single entry point (Front Controller pattern)
// Routes all requests to the correct role controller.

require_once __DIR__ . '/controllers/SeekerController.php';

$action = trim($_GET['action'] ?? 'login');

// Only Job Seeker role is handled here.
// Other roles (employer, recruiter, admin) would be separate entry points
// or additional routing logic below.
$controller = new SeekerController();
$controller->dispatch($action);