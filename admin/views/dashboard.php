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
        <h1>Admin Dashboard</h1>
    </div>
</section>

<div class="metric-grid admin-metrics">
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['total_users'] ?></span><span class="metric-label">Total Users</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['active_jobs'] ?></span><span class="metric-label">Active Jobs</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['applications_today'] ?></span><span class="metric-label">Applications Today</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['pending_verifications'] ?></span><span class="metric-label">Pending Verification</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['open_complaints'] ?></span><span class="metric-label">Open Complaints</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['featured_jobs'] ?></span><span class="metric-label">Featured Jobs</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['total_applications'] ?></span><span class="metric-label">Total Applications</span></div>
    <div class="metric-card"><span class="metric-val"><?= (int)$stats['role_counts']['admin'] ?></span><span class="metric-label">Admins</span></div>
</div>

<div class="admin-grid two-col-admin">
    <section class="card">
        <h2>Users by Role</h2>
        <table class="data-table">
            <tbody>
                <?php foreach ($stats['role_counts'] as $role => $count): ?>
                <tr><th><?= e(ucfirst($role)) ?></th><td><?= (int)$count ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Pending Employer/Recruiter Verification</h2>
        <?php if (empty($pendingAccounts)): ?>
            <p class="muted">No pending verification requests.</p>
        <?php else: ?>
            <table class="data-table compact-table">
                <thead><tr><th>Name</th><th>Role</th><th>Company/Agency</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($pendingAccounts as $account): ?>
                    <tr>
                        <td><?= e($account['name']) ?><br><small><?= e($account['email']) ?></small></td>
                        <td><span class="badge"><?= e($account['role']) ?></span></td>
                        <td><?= e($account['company_name'] ?: $account['agency_name'] ?: '—') ?></td>
                        <td><a class="btn-sm" href="index.php?action=<?= e($account['role'] === 'employer' ? 'employers' : 'recruiters') ?>&status=pending">Review</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>

<div class="admin-grid two-col-admin">
    <section class="card">
        <h2>Recent Complaints</h2>
        <?php if (empty($recentComplaints)): ?>
            <p class="muted">No complaints submitted yet.</p>
        <?php else: ?>
            <table class="data-table compact-table">
                <thead><tr><th>Submitter</th><th>Status</th><th>Created</th></tr></thead>
                <tbody>
                <?php foreach ($recentComplaints as $complaint): ?>
                    <tr>
                        <td><?= e($complaint['submitter_name']) ?><br><small><?= e($complaint['description']) ?></small></td>
                        <td><span class="status-badge status-<?= e($complaint['status']) ?>"><?= e($complaint['status']) ?></span></td>
                        <td><?= fmt_datetime($complaint['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <a class="btn-sm" href="index.php?action=complaints">Handle complaints</a>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Recent Admin Actions</h2>
        <?php if (empty($logs)): ?>
            <p class="muted">No admin actions recorded yet.</p>
        <?php else: ?>
            <ul class="activity-list">
                <?php foreach ($logs as $log): ?>
                <li>
                    <strong><?= e($log['action']) ?></strong> <?= e($log['target_type']) ?>
                    <?php if (!empty($log['target_name'])): ?>: <?= e($log['target_name']) ?><?php endif; ?>
                    <small><?= fmt_datetime($log['created_at']) ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

</main>
</body>
</html>
