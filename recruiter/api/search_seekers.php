<?php
/**
 * AJAX endpoint: search seekers
 */
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/SeekerModel.php';

header('Content-Type: application/json');

$model = new SeekerModel($conn);

$keyword    = trim($_GET['keyword'] ?? '');
$exp_min    = $_GET['exp_min'] ?? '';
$location   = trim($_GET['location'] ?? '');
$salary_max = $_GET['salary_max'] ?? '';

// If nothing searched, return empty
if ($keyword === '' && $exp_min === '' && $location === '' && $salary_max === '') {
    echo json_encode([]);
    exit();
}

$results = $model->searchSeekers($keyword, $exp_min, $location, $salary_max);

// Format for JSON
$formatted = array_map(function($s) {
    return [
        'id'                 => $s['seeker_id'],
        'name'               => htmlspecialchars($s['name']),
        'headline'           => htmlspecialchars($s['headline'] ?? 'No headline'),
        'skills'             => htmlspecialchars($s['skills'] ?? 'No skills listed'),
        'years_experience'   => (int)$s['years_experience'],
        'preferred_location' => htmlspecialchars($s['preferred_location'] ?? 'Any'),
        'expected_salary'    => $s['expected_salary'] ? number_format($s['expected_salary']) : 'Negotiable'
    ];
}, $results);

echo json_encode($formatted);
?>
