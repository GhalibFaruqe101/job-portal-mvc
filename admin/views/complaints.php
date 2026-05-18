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
        <h1>Complaints & Disputes</h1>
    </div>
</section>
<section class="card">
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="action" value="complaints">
        <div class="form-group inline-field"><label>Status</label><select name="status" class="form-control">
            <option value="all" <?= selected_attr($status, 'all') ?>>All</option>
            <option value="open" <?= selected_attr($status, 'open') ?>>Open</option>
            <option value="resolved" <?= selected_attr($status, 'resolved') ?>>Resolved</option>
        </select></div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</section>
<section class="card">
    <h2>Submitted Complaints</h2>
    <?php if (empty($complaints)): ?>
        <p class="muted">No complaints found.</p>
    <?php else: ?>
        <?php foreach ($complaints as $complaint): ?>
        <article class="complaint-card" data-complaint-row="<?= (int)$complaint['id'] ?>">
            <div class="complaint-top">
                <div>
                    <h3>Complaint #<?= (int)$complaint['id'] ?></h3>
                    <p class="muted">By <?= e($complaint['submitter_name']) ?> (<?= e($complaint['submitter_role']) ?>) · <?= fmt_datetime($complaint['created_at']) ?></p>
                    <?php if ($complaint['subject_name']): ?><p class="muted">Subject: <?= e($complaint['subject_name']) ?> (<?= e($complaint['subject_role']) ?>)</p><?php endif; ?>
                </div>
                <span class="status-badge status-<?= e($complaint['status']) ?>"><?= e($complaint['status']) ?></span>
            </div>
            <p><?= nl2br(e($complaint['description'])) ?></p>
            <?php if ($complaint['admin_note']): ?><div class="admin-note"><strong>Admin note:</strong> <?= nl2br(e($complaint['admin_note'])) ?></div><?php endif; ?>
            <?php if ($complaint['status'] === 'open'): ?>
            <div class="resolve-box">
                <textarea class="form-control complaint-note" rows="3" placeholder="Write resolution note..."></textarea>
                <button type="button" class="btn-primary ajax-complaint-resolve" data-complaint-id="<?= (int)$complaint['id'] ?>">Mark Resolved</button>
            </div>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</main>
</body>
</html>
