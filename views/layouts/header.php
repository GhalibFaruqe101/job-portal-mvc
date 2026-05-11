<!DOCTYPE html>
<!-- views/layouts/header.php — Shared header included by all seeker views -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'HireLink') ?> — HireLink</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="navbar">
    <a class="brand" href="<?= BASE_URL ?>index.php?action=dashboard">
        <span class="brand-icon">💼</span> HireLink
    </a>
    <ul class="nav-links">
        <li><a href="<?= BASE_URL ?>index.php?action=dashboard"  <?= ($activeNav??'')=='dashboard'  ? 'class="active"':'' ?>>Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>index.php?action=jobs"       <?= ($activeNav??'')=='jobs'       ? 'class="active"':'' ?>>Find Jobs</a></li>
        <li><a href="<?= BASE_URL ?>index.php?action=applications" <?= ($activeNav??'')=='applications'?'class="active"':'' ?>>Applications</a></li>
        <li><a href="<?= BASE_URL ?>index.php?action=savedJobs"  <?= ($activeNav??'')=='saved'      ? 'class="active"':'' ?>>Saved</a></li>
        <li><a href="<?= BASE_URL ?>index.php?action=alerts"     <?= ($activeNav??'')=='alerts'     ? 'class="active"':'' ?>>Alerts</a></li>
        <li><a href="<?= BASE_URL ?>index.php?action=messages"   <?= ($activeNav??'')=='messages'   ? 'class="active"':'' ?>>Messages</a></li>
    </ul>
    <div class="nav-user">
        <a href="<?= BASE_URL ?>index.php?action=profile" class="user-link">
            <?= htmlspecialchars($_SESSION['name'] ?? 'Profile') ?>
        </a>
        <a href="<?= BASE_URL ?>index.php?action=logout" class="btn-logout">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="main-content">

<?php if (!empty($flash)): ?>
    <div class="flash-msg"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>