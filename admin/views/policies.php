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
        <h1>Platform Policies</h1>
    </div>
</section>
<?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="card">
    <form method="post" action="index.php?action=savePolicies" class="admin-form narrow-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <div class="form-group"><label>Maximum job postings per employer</label><input type="number" min="1" name="max_jobs_per_employer" class="form-control" value="<?= e($policies['max_jobs_per_employer']) ?>" required></div>
        <div class="form-group"><label>Maximum active applications per seeker</label><input type="number" min="1" name="max_active_applications_per_seeker" class="form-control" value="<?= e($policies['max_active_applications_per_seeker']) ?>" required></div>
        <div class="form-group"><label>Resume visibility default</label><select name="resume_visibility_default" class="form-control">
            <option value="private" <?= selected_attr($policies['resume_visibility_default'], 'private') ?>>Private</option>
            <option value="public" <?= selected_attr($policies['resume_visibility_default'], 'public') ?>>Public</option>
        </select></div>
        <button type="submit" class="btn-primary">Save Policies</button>
    </form>
</section>

</main>
</body>
</html>
