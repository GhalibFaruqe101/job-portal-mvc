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
        <h1>All Job Postings</h1>
    </div>
</section>
<?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

<section class="card">
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="action" value="jobs">
        <div class="form-group inline-field"><label>Status</label><select name="status" class="form-control">
            <option value="" <?= selected_attr($filters['status'], '') ?>>All</option>
            <option value="active" <?= selected_attr($filters['status'], 'active') ?>>Active</option>
            <option value="closed" <?= selected_attr($filters['status'], 'closed') ?>>Closed</option>
            <option value="draft" <?= selected_attr($filters['status'], 'draft') ?>>Draft</option>
        </select></div>
        <div class="form-group inline-field"><label>Employer</label><select name="employer_id" class="form-control">
            <option value="0">All</option>
            <?php foreach ($options['employers'] as $emp): ?><option value="<?= (int)$emp['id'] ?>" <?= selected_attr($filters['employer_id'], $emp['id']) ?>><?= e($emp['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="form-group inline-field"><label>Recruiter</label><select name="recruiter_id" class="form-control">
            <option value="0">All</option>
            <?php foreach ($options['recruiters'] as $rec): ?><option value="<?= (int)$rec['id'] ?>" <?= selected_attr($filters['recruiter_id'], $rec['id']) ?>><?= e($rec['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="form-group inline-field grow"><label>Search</label><input type="text" name="search" class="form-control" value="<?= e($filters['search']) ?>"></div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</section>

<section class="card">
    <h2>Jobs</h2>
    <?php if (empty($jobs)): ?>
        <p class="muted">No jobs found.</p>
    <?php else: ?>
    <table class="data-table responsive-table">
        <thead><tr><th>Job</th><th>Employer / Recruiter</th><th>Category</th><th>Status</th><th>Applications</th><th>Featured</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($jobs as $job): ?>
        <tr data-job-row="<?= (int)$job['id'] ?>">
            <td><strong><?= e($job['title']) ?></strong><br><small><?= e($job['location']) ?> · <?= e($job['job_type']) ?> · Deadline <?= fmt_date($job['deadline']) ?></small></td>
            <td><?= e($job['company_name'] ?: $job['employer_user_name']) ?><?php if ($job['recruiter_id']): ?><br><small>via <?= e($job['agency_name'] ?: $job['recruiter_user_name']) ?></small><?php endif; ?></td>
            <td><?= e($job['category_name']) ?></td>
            <td><span class="status-badge status-<?= e($job['status']) ?>"><?= e($job['status']) ?></span></td>
            <td><?= (int)$job['application_count'] ?></td>
            <td><button type="button" class="btn-sm ajax-featured-toggle" data-job-id="<?= (int)$job['id'] ?>"><?= (int)$job['is_featured'] ? 'Featured' : 'Set Featured' ?></button></td>
            <td class="action-cell">
                <form method="post" action="index.php?action=saveJobStatus" class="inline-form-tight">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
                    <select name="status" class="form-control tiny-select">
                        <option value="active" <?= selected_attr($job['status'], 'active') ?>>active</option>
                        <option value="closed" <?= selected_attr($job['status'], 'closed') ?>>closed</option>
                        <option value="draft" <?= selected_attr($job['status'], 'draft') ?>>draft</option>
                    </select>
                    <button type="submit" class="btn-sm">Save</button>
                </form>
                <form method="post" action="index.php?action=deleteJob" onsubmit="return confirm('Remove this job from platform?');" class="inline-form-tight">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
                    <button type="submit" class="btn-sm btn-danger">Remove</button>
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
