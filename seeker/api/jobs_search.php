<?php
// api/jobs_search.php — AJAX endpoint for job search. Returns JSON.
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/SeekerModel.php';

$model   = new SeekerModel();
$keyword = trim($_GET['q'] ?? '');
$catId   = (int)($_GET['category'] ?? 0);
$loc     = trim($_GET['location'] ?? '');
$type    = trim($_GET['job_type'] ?? '');
$lvl     = trim($_GET['exp_level'] ?? '');
$sMin    = (float)($_GET['sal_min'] ?? 0);
$sMax    = (float)($_GET['sal_max'] ?? 0);
$page    = max(1, (int)($_GET['page'] ?? 1));

[$jobs, $total] = $model->searchJobs($keyword, $catId, $loc, $type, $lvl, $sMin, $sMax, $page);

$result = array_map(fn($j) => [
    'id'               => $j['id'],
    'title'            => $j['title'],
    'company_name'     => $j['company_name'] ?? 'Unknown',
    'agency_name'      => $j['agency_name'] ?? null,
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

echo json_encode(['count' => $total, 'jobs' => $result, 'page' => $page]);
