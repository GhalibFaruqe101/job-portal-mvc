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
        <h1>Featured Job Listings</h1>
    </div>
</section>
<section class="card">
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="action" value="featured">
        <div class="form-group inline-field grow"><label>Search featured jobs</label><input type="text" name="search" value="<?= e($search) ?>" class="form-control"></div>
        <button type="submit" class="btn-primary">Search</button>
        <a href="index.php?action=jobs" class="btn-sm">Feature more jobs from All Jobs</a>
    </form>
</section>
<section class="card">
    <h2>Currently Featured</h2>
    <?php if (empty($jobs)): ?>
        <p class="muted">No featured jobs yet. Go to All Jobs and click Set Featured.</p>
    <?php else: ?>
    <table class="data-table responsive-table">
        <thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Deadline</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($jobs as $job): ?>
            <tr data-job-row="<?= (int)$job['id'] ?>">
                <td><strong><?= e($job['title']) ?></strong><br><small><?= e($job['location']) ?> · <?= e($job['job_type']) ?></small></td>
                <td><?= e($job['company_name'] ?: $job['employer_user_name']) ?></td>
                <td><span class="status-badge status-<?= e($job['status']) ?>"><?= e($job['status']) ?></span></td>
                <td><?= fmt_date($job['deadline']) ?></td>
                <td><button type="button" class="btn-sm btn-danger ajax-featured-toggle" data-job-id="<?= (int)$job['id'] ?>">Remove Featured</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

</main>
</body>
</html>
