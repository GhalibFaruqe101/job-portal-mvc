<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/AnalyticsModel.php';

$model = new AnalyticsModel($conn);
$recruiter_id = $_SESSION['user_id'];

$clientReports = $model->getClientReport($recruiter_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Performance Report - JobPortal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/analytics.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="global-nav">
    <a href="dashboard.php" class="logo">JobPortal <span style="font-size:0.8rem;color:#8b5cf6;">[Recruiter]</span></a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php">Clients</a>
        <a href="jobs.php">Jobs</a>
        <a href="seekers.php">Seekers</a>
        <a href="outreach.php">Outreach</a>
        <a href="candidates.php">Candidates</a>
        <a href="analytics.php" class="active">Analytics</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="analytics-main">
    <div class="page-header">
        <div>
            <h1>Client Report</h1>
            <p>Analyze performance and hiring success rates per client.</p>
        </div>
        <div class="header-actions">
            <a href="analytics.php" class="btn-outline">&larr; Back to Analytics</a>
        </div>
    </div>

    <div class="analytics-card">
        <?php if (empty($clientReports)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏢</div>
                <p>No clients found. Add clients and post jobs to see reports.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Client / Company</th>
                            <th>Jobs Posted</th>
                            <th>Total Applications</th>
                            <th>Successful Hires</th>
                            <th>Conversion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientReports as $cr): ?>
                            <?php 
                                $rate = 0;
                                if ($cr['total_applications'] > 0) {
                                    $rate = round(($cr['total_hired'] / $cr['total_applications']) * 100, 1);
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($cr['client_name']); ?></strong>
                                    <?php if($cr['employer_id']): ?>
                                        <span class="badge-linked">Linked</span>
                                    <?php else: ?>
                                        <span class="badge-standalone">Standalone</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $cr['jobs_posted']; ?></td>
                                <td><?php echo $cr['total_applications']; ?></td>
                                <td><span class="text-green font-bold"><?php echo $cr['total_hired']; ?></span></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $rate; ?>%;"></div>
                                    </div>
                                    <span class="text-muted" style="font-size:0.8rem;"><?php echo $rate; ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
