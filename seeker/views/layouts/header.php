<!DOCTYPE html>
<?php
// header.php - BASE_PATH is set by config/app.php (loaded via index.php)
// e.g. BASE_PATH = /job_portal/seeker  (auto-detected, works on any machine)
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'JobPortal') ?> — JobPortal</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/style.css">
</head>
<body>

<?php if (!empty($_SESSION['user_id'])): ?>
<?php
// Unread message count for badge
require_once APP_ROOT . '/config/db.php';
$_db  = getDB();
$_uid = (int)$_SESSION['user_id'];
$_r   = $_db->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND is_read = 0");
$_r->bind_param('i', $_uid);
$_r->execute();
$_r->bind_result($_unread);
$_r->fetch();
$_r->close();
?>
<nav class="navbar">
    <a class="brand" href="<?= BASE_PATH ?>/index.php?action=dashboard">
         JobPortal
    </a>
    <ul class="nav-links">
        <li><a href="<?= BASE_PATH ?>/index.php?action=dashboard"    <?= ($activeNav??'')=='dashboard'    ? 'class="active"':'' ?>>Dashboard</a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=jobs"         <?= ($activeNav??'')=='jobs'         ? 'class="active"':'' ?>>Find Jobs</a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=applications" <?= ($activeNav??'')=='applications' ? 'class="active"':'' ?>>Applications</a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=savedJobs"    <?= ($activeNav??'')=='saved'        ? 'class="active"':'' ?>>Saved</a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=alerts"       <?= ($activeNav??'')=='alerts'       ? 'class="active"':'' ?>>Alerts</a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=messages"     <?= ($activeNav??'')=='messages'     ? 'class="active"':'' ?>>Messages<?php if ($_unread > 0): ?> <span class="nav-badge"><?= $_unread ?></span><?php endif; ?></a></li>
        <li><a href="<?= BASE_PATH ?>/index.php?action=outreach"     <?= ($activeNav??'')=='outreach'     ? 'class="active"':'' ?>>Outreach</a></li>
    </ul>
    <div class="nav-user">
        <a href="<?= BASE_PATH ?>/index.php?action=profile" class="user-link">
            <?= htmlspecialchars($_SESSION['name'] ?? 'Profile') ?>
        </a>
        <a href="<?= BASE_PATH ?>/index.php?action=logout" class="btn-logout">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="main-content">

<?php if (!empty($flash)): ?>
    <div class="flash-msg"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
