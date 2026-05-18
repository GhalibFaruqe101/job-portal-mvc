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
        <h1>Seeker Account Management</h1>
    </div>
</section>

<section class="card">
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="action" value="seekers">
        <div class="form-group inline-field">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="all" <?= selected_attr($status, 'all') ?>>All</option>
                <option value="pending" <?= selected_attr($status, 'pending') ?>>Pending</option>
                <option value="verified" <?= selected_attr($status, 'verified') ?>>Verified / Active</option>
                <option value="suspended" <?= selected_attr($status, 'suspended') ?>>Suspended / Inactive</option>
            </select>
        </div>
        <div class="form-group inline-field grow">
            <label>Search</label>
            <input type="text" name="search" class="form-control" value="<?= e($search) ?>" placeholder="Name, email, phone, company, skill...">
        </div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</section>

<section class="card">
    <h2>Accounts</h2>
    <?php if (empty($accounts)): ?>
        <p class="muted">No accounts found.</p>
    <?php else: ?>
    <table class="data-table responsive-table">
        <thead><tr><th>Name</th><th>Profile</th><th>Contact</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($accounts as $account): ?>
        <tr data-user-row="<?= (int)$account['id'] ?>">
            <td><strong><?= e($account['name']) ?></strong><br><small>#<?= (int)$account['id'] ?> · <?= e($account['role']) ?></small></td>
            <td>
                <?php if ($account['role'] === 'employer'): ?>
                    <?= e($account['company_name'] ?: 'No company profile') ?><br><small><?= e($account['industry'] ?: '') ?></small>
                <?php elseif ($account['role'] === 'recruiter'): ?>
                    <?= e($account['agency_name'] ?: 'No recruiter profile') ?><br><small><?= e($account['specialization'] ?: '') ?></small>
                <?php else: ?>
                    <?= e($account['headline'] ?: 'No headline') ?><br><small><?= e($account['preferred_location'] ?: '') ?></small>
                <?php endif; ?>
            </td>
            <td><?= e($account['email']) ?><br><small><?= e($account['phone']) ?></small></td>
            <td>
                <span class="status-badge <?= (int)$account['is_active'] ? 'status-active' : 'status-rejected' ?>"><?= (int)$account['is_active'] ? 'Active' : 'Inactive' ?></span>
                <span class="status-badge <?= (int)$account['is_verified'] ? 'status-shortlisted' : 'status-reviewed' ?>"><?= (int)$account['is_verified'] ? 'Verified' : 'Unverified' ?></span>
            </td>
            <td><?= fmt_datetime($account['created_at']) ?></td>
            <td class="action-cell">
                <a class="btn-sm" href="index.php?action=userView&id=<?= (int)$account['id'] ?>">View</a>
                <?php if ($account['role'] !== 'seeker'): ?>
                    <button type="button" class="btn-sm ajax-account-action" data-user-id="<?= (int)$account['id'] ?>" data-action="approve">Approve</button>
                    <button type="button" class="btn-sm btn-danger ajax-account-action" data-user-id="<?= (int)$account['id'] ?>" data-action="reject" data-needs-reason="1">Reject</button>
                <?php endif; ?>
                <?php if ((int)$account['is_active'] === 1): ?>
                    <button type="button" class="btn-sm btn-danger ajax-account-action" data-user-id="<?= (int)$account['id'] ?>" data-action="suspend" data-needs-reason="1">Suspend</button>
                <?php else: ?>
                    <button type="button" class="btn-sm ajax-account-action" data-user-id="<?= (int)$account['id'] ?>" data-action="reactivate">Reactivate</button>
                <?php endif; ?>
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
