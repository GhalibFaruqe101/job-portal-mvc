<?php
// api/jobs_search.php — AJAX endpoint for job search filters
// Returns JSON. Requires active seeker session.

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/SeekerModel.php';

$model = new SeekerModel();

$keyword  = trim($_GET['q']          ?? '');
$catId    = (int)($_GET['category']  ?? 0);
$location = trim($_GET['location']   ?? '');
$jobType  = trim($_GET['job_type']   ?? '');
$expLevel = trim($_GET['exp_level']  ?? '');
$salMin   = (float)($_GET['sal_min'] ?? 0);
$salMax   = (float)($_GET['sal_max'] ?? 0);

$jobs = $model->searchJobs($keyword, $catId, $location, $jobType, $expLevel, $salMin, $salMax);

$result = array_map(fn($j) => [
    'id'               => $j['id'],
    'title'            => $j['title'],
    'company_name'     => $j['company_name'] ?? 'Unknown',
    'agency_name'      => $j['agency_name']  ?? null,
    'location'         => $j['location'],
    'job_type'         => $j['job_type'],
    'experience_level' => $j['experience_level'],
    'salary_min'       => $j['salary_min'],
    'salary_max'       => $j['salary_max'],
    'deadline'         => $j['deadline'],
    'is_featured'      => (bool)$j['is_featured'],
    'category_name'    => $j['category_name'],
    'logo_path'        => $j['logo_path'] ?? '',
], $jobs);

echo json_encode(['count' => count($result), 'jobs' => $result]);
