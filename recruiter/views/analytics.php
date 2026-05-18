<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/AnalyticsModel.php';

$model = new AnalyticsModel($conn);
$recruiter_id = $_SESSION['user_id'];

$stats = $model->getOverviewStats($recruiter_id);
$recentPlacements = $model->getPlacements($recruiter_id, 5); // Just top 5 for overview
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Overview - JobPortal Recruiter</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/recruiter_base.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/analytics.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/recruiter_nav.php'; ?>

<main class="analytics-main">
    <div class="page-header">
        <div>
            <h1>Analytics & Reporting</h1>
            <p>Measure your recruiting performance and placement success.</p>
        </div>
        <div class="header-actions">
            <a href="placements.php" class="btn-outline">View All Placements</a>
            <a href="client_report.php" class="btn-primary">Client Report</a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue">💼</div>
            <div class="kpi-data">
                <h3>Active Jobs</h3>
                <div class="kpi-value"><?php echo $stats['total_jobs']; ?></div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon purple">👥</div>
            <div class="kpi-data">
                <h3>Total Candidates</h3>
                <div class="kpi-value"><?php echo $stats['total_candidates']; ?></div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green">🏆</div>
            <div class="kpi-data">
                <h3>Successful Hires</h3>
                <div class="kpi-value"><?php echo $stats['total_hired']; ?></div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon orange">🏢</div>
            <div class="kpi-data">
                <h3>Active Clients</h3>
                <div class="kpi-value"><?php echo $stats['total_clients']; ?></div>
            </div>
        </div>
    </div>

    <!-- Recent Placements Section -->
    <div class="analytics-card">
        <div class="card-header">
            <h2>Recent Placements</h2>
            <a href="placements.php" class="view-all">See All &rarr;</a>
        </div>
        
        <?php if (empty($recentPlacements)): ?>
            <div class="empty-state">
                <div class="empty-icon">📈</div>
                <p>No placements yet. Keep pushing candidates through the pipeline!</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Hired Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPlacements as $p): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($p['seeker_name']); ?></strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($p['seeker_email']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($p['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($p['client_name']); ?></td>
                                <td>
                                    <span class="badge-success">Hired</span> 
                                    <?php echo date('d M Y', strtotime($p['applied_at'])); ?>
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


