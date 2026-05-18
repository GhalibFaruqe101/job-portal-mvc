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
        <h1>Job Categories</h1>
    </div>
</section>
<?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

<section class="card">
    <h2>Add New Category</h2>
    <form method="post" action="index.php?action=saveCategory" class="admin-form grid-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Description</label><input type="text" name="description" class="form-control"></div>
        <button type="submit" class="btn-primary">Add Category</button>
    </form>
</section>

<section class="card">
    <h2>Existing Categories</h2>
    <?php if (empty($categories)): ?>
        <p class="muted">No categories found.</p>
    <?php else: ?>
    <table class="data-table responsive-table">
        <thead><tr><th>Name</th><th>Description</th><th>Total Jobs</th><th>Active Jobs</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
            <form method="post" action="index.php?action=saveCategory">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                <td><input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required></td>
                <td><input type="text" name="description" class="form-control" value="<?= e($cat['description']) ?>"></td>
                <td><?= (int)$cat['total_jobs'] ?></td>
                <td><?= (int)$cat['active_jobs'] ?></td>
                <td class="action-cell"><button type="submit" class="btn-sm">Save</button>
            </form>
            <form method="post" action="index.php?action=deleteCategory" onsubmit="return confirm('Delete this category?');" class="inline-form-tight">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                <button type="submit" class="btn-sm btn-danger">Delete</button>
            </form>
                </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

</main>
</body>
</html>
