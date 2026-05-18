<?php
require_once __DIR__ . '/../helpers/session.php';
require_role('admin');
require_once __DIR__ . '/../helpers/view.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Job Portal</title>
    <meta name="csrf-token" content="<?= e($csrfToken ?? admin_csrf_token()) ?>">
    <link rel="stylesheet" href="<?= e($assetBase ?? '../') ?>public/css/style.css">
    <link rel="stylesheet" href="<?= e($assetBase ?? '../') ?>public/css/admin/admin.css">
    <script defer src="<?= e($assetBase ?? '../') ?>public/js/admin/admin.js"></script>
</head>
<body>
<nav class="global-nav admin-nav">
    <a href="index.php?action=dashboard" class="logo">JobPortal Admin</a>
    <div class="nav-links">
        <a href="index.php?action=dashboard" <?= active_class($activeNav ?? '', 'dashboard') ?>>Dashboard</a>
        <a href="index.php?action=employers" <?= active_class($activeNav ?? '', 'employers') ?>>Employers</a>
        <a href="index.php?action=recruiters" <?= active_class($activeNav ?? '', 'recruiters') ?>>Recruiters</a>
        <a href="index.php?action=seekers" <?= active_class($activeNav ?? '', 'seekers') ?>>Seekers</a>
        <a href="index.php?action=categories" <?= active_class($activeNav ?? '', 'categories') ?>>Categories</a>
        <a href="index.php?action=jobs" <?= active_class($activeNav ?? '', 'jobs') ?>>Jobs</a>
        <a href="index.php?action=featured" <?= active_class($activeNav ?? '', 'featured') ?>>Featured</a>
        <a href="index.php?action=complaints" <?= active_class($activeNav ?? '', 'complaints') ?>>Complaints</a>
        <a href="index.php?action=policies" <?= active_class($activeNav ?? '', 'policies') ?>>Policies</a>
        <a href="index.php?action=analytics" <?= active_class($activeNav ?? '', 'analytics') ?>>Analytics</a>
        <a href="index.php?action=announcements" <?= active_class($activeNav ?? '', 'announcements') ?>>Announcements</a>
        <a href="index.php?action=monthlyReport" <?= active_class($activeNav ?? '', 'monthly') ?>>Monthly Report</a>
        <a href="index.php?action=logout">Logout</a>
    </div>
</nav>
<main class="admin-main">

<section class="page-heading">
    <div>
        <h1>Platform Analytics</h1>
    </div>
</section>
<div class="admin-grid two-col-admin">
    <section class="card"><h2>Jobs Posted per Category</h2><?php include_table($analytics['jobs_by_category'], ['name' => 'Category', 'total' => 'Jobs']); ?></section>
    <section class="card"><h2>Application Volume Over Time</h2><?php include_table($analytics['application_volume'], ['day' => 'Date', 'total' => 'Applications']); ?></section>
    <section class="card"><h2>Top Performing Employers</h2><?php include_table($analytics['top_employers'], ['employer_name' => 'Employer', 'total_applications' => 'Applications']); ?></section>
    <section class="card"><h2>Most Active Recruiters</h2><?php include_table($analytics['active_recruiters'], ['recruiter_name' => 'Recruiter', 'posted_jobs' => 'Jobs Posted']); ?></section>
    <section class="card"><h2>Popular Locations</h2><?php include_table($analytics['popular_locations'], ['location' => 'Location', 'total' => 'Jobs']); ?></section>
    <section class="card"><h2>Popular Job Types</h2><?php include_table($analytics['popular_job_types'], ['job_type' => 'Job Type', 'total' => 'Jobs']); ?></section>
</div>
<?php
function include_table($rows, $columns) {
    if (empty($rows)) { echo '<p class="muted">No data yet.</p>'; return; }
    echo '<table class="data-table compact-table"><thead><tr>';
    foreach ($columns as $label) echo '<th>' . e($label) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($columns as $key => $label) echo '<td>' . e($row[$key] ?? '—') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>

</main>
</body>
</html>
