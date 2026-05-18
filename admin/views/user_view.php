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
        <h1>User Details</h1>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php elseif ($user): ?>
<section class="card">
    <div class="profile-admin-header">
        <div>
            <h2><?= e($user['name']) ?></h2>
            <p><?= e($user['email']) ?> · <?= e($user['phone']) ?></p>
            <p>
                <span class="badge"><?= e($user['role']) ?></span>
                <span class="status-badge <?= (int)$user['is_active'] ? 'status-active' : 'status-rejected' ?>"><?= (int)$user['is_active'] ? 'Active' : 'Inactive' ?></span>
                <span class="status-badge <?= (int)$user['is_verified'] ? 'status-shortlisted' : 'status-reviewed' ?>"><?= (int)$user['is_verified'] ? 'Verified' : 'Unverified' ?></span>
            </p>
        </div>
        <div class="action-cell">
            <?php if ($user['role'] !== 'seeker'): ?>
                <button type="button" class="btn-sm ajax-account-action" data-user-id="<?= (int)$user['id'] ?>" data-action="approve">Approve</button>
                <button type="button" class="btn-sm btn-danger ajax-account-action" data-user-id="<?= (int)$user['id'] ?>" data-action="reject" data-needs-reason="1">Reject</button>
            <?php endif; ?>
            <?php if ((int)$user['is_active'] === 1): ?>
                <button type="button" class="btn-sm btn-danger ajax-account-action" data-user-id="<?= (int)$user['id'] ?>" data-action="suspend" data-needs-reason="1">Suspend</button>
            <?php else: ?>
                <button type="button" class="btn-sm ajax-account-action" data-user-id="<?= (int)$user['id'] ?>" data-action="reactivate">Reactivate</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="card">
    <h2>Profile Information</h2>
    <table class="data-table detail-table">
        <tbody>
            <tr><th>User ID</th><td>#<?= (int)$user['id'] ?></td></tr>
            <tr><th>Created</th><td><?= fmt_datetime($user['created_at']) ?></td></tr>
            <?php if ($user['role'] === 'employer'): ?>
                <tr><th>Company Name</th><td><?= e($user['company_name']) ?></td></tr>
                <tr><th>Industry</th><td><?= e($user['industry']) ?></td></tr>
                <tr><th>Company Size</th><td><?= e($user['company_size']) ?></td></tr>
                <tr><th>Website</th><td><?= e($user['company_website']) ?></td></tr>
                <tr><th>Address</th><td><?= e($user['address']) ?></td></tr>
                <tr><th>Description</th><td><?= nl2br(e($user['company_description'])) ?></td></tr>
            <?php elseif ($user['role'] === 'recruiter'): ?>
                <tr><th>Agency Name</th><td><?= e($user['agency_name']) ?></td></tr>
                <tr><th>Specialization</th><td><?= e($user['specialization']) ?></td></tr>
                <tr><th>Website</th><td><?= e($user['recruiter_website']) ?></td></tr>
                <tr><th>Description</th><td><?= nl2br(e($user['recruiter_description'])) ?></td></tr>
            <?php elseif ($user['role'] === 'seeker'): ?>
                <tr><th>Headline</th><td><?= e($user['headline']) ?></td></tr>
                <tr><th>Summary</th><td><?= nl2br(e($user['summary'])) ?></td></tr>
                <tr><th>Skills</th><td><?= e($user['skills']) ?></td></tr>
                <tr><th>Experience</th><td><?= e($user['years_experience']) ?> years</td></tr>
                <tr><th>Education</th><td><?= e($user['education_level']) ?></td></tr>
                <tr><th>Expected Salary</th><td><?= money_bdt($user['expected_salary']) ?></td></tr>
                <tr><th>Preferred Location</th><td><?= e($user['preferred_location']) ?></td></tr>
                <tr><th>Resume</th><td><?= $user['resume_path'] ? '<a href="../' . e($user['resume_path']) . '" target="_blank">View Resume</a>' : '—' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?php endif; ?>

</main>
</body>
</html>
