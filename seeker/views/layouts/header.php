<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'JobPortal') ?> — JobPortal</title>
    <link rel="stylesheet" href="/job_portal/seeker/assets/css/style.css">
</head>
<body>

<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="navbar">
    <a class="brand" href="/job_portal/seeker/index.php?action=dashboard">
        💼 JobPortal
    </a>
    <ul class="nav-links">
        <li><a href="/job_portal/seeker/index.php?action=dashboard"    <?= ($activeNav??'')=='dashboard'    ? 'class="active"':'' ?>>Dashboard</a></li>
        <li><a href="/job_portal/seeker/index.php?action=jobs"         <?= ($activeNav??'')=='jobs'         ? 'class="active"':'' ?>>Find Jobs</a></li>
        <li><a href="/job_portal/seeker/index.php?action=applications" <?= ($activeNav??'')=='applications' ? 'class="active"':'' ?>>Applications</a></li>
        <li><a href="/job_portal/seeker/index.php?action=savedJobs"    <?= ($activeNav??'')=='saved'        ? 'class="active"':'' ?>>Saved</a></li>
        <li><a href="/job_portal/seeker/index.php?action=alerts"       <?= ($activeNav??'')=='alerts'       ? 'class="active"':'' ?>>Alerts</a></li>
        <li><a href="/job_portal/seeker/index.php?action=messages"     <?= ($activeNav??'')=='messages'     ? 'class="active"':'' ?>>Messages
            <?php
            // Unread message count badge
            if (!empty($_SESSION['user_id'])) {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/job_portal/seeker/config/db.php';
                $db = getDB();
                $uid = (int)$_SESSION['user_id'];
                $r = $db->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND is_read = 0");
                $r->bind_param('i', $uid);
                $r->execute();
                $r->bind_result($unread);
                $r->fetch();
                if ($unread > 0) echo " <span class='nav-badge'>$unread</span>";
            }
            ?>
        </a></li>
        <li><a href="/job_portal/seeker/index.php?action=outreach"     <?= ($activeNav??'')=='outreach'     ? 'class="active"':'' ?>>Outreach</a></li>
    </ul>
    <div class="nav-user">
        <a href="/job_portal/seeker/index.php?action=profile" class="user-link">
            <?= htmlspecialchars($_SESSION['name'] ?? 'Profile') ?>
        </a>
        <a href="/job_portal/seeker/index.php?action=logout" class="btn-logout">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="main-content">

<?php if (!empty($flash)): ?>
    <div class="flash-msg"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
