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
        <h1>Platform Announcements</h1>
    </div>
</section>
<?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="card">
    <h2>Post New Announcement</h2>
    <form method="post" action="index.php?action=saveAnnouncement" class="admin-form narrow-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="form-group"><label>Message</label><textarea name="body" rows="5" class="form-control" required></textarea></div>
        <label class="check-row"><input type="checkbox" name="is_active" checked> Active / visible</label>
        <button type="submit" class="btn-primary">Post Announcement</button>
    </form>
</section>
<section class="card">
    <h2>Announcement History</h2>
    <?php if (empty($announcements)): ?>
        <p class="muted">No announcements posted yet.</p>
    <?php else: ?>
    <table class="data-table responsive-table">
        <thead><tr><th>Title</th><th>Message</th><th>Status</th><th>Posted By</th><th>Created</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($announcements as $a): ?>
        <tr>
            <td><strong><?= e($a['title']) ?></strong></td>
            <td><?= nl2br(e($a['body'])) ?></td>
            <td><span class="status-badge <?= (int)$a['is_active'] ? 'status-active' : 'status-reviewed' ?>"><?= (int)$a['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td><?= e($a['admin_name']) ?></td>
            <td><?= fmt_datetime($a['created_at']) ?></td>
            <td><form method="post" action="index.php?action=toggleAnnouncement"><input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><button type="submit" class="btn-sm"><?= (int)$a['is_active'] ? 'Deactivate' : 'Activate' ?></button></form></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

</main>
</body>
</html>
