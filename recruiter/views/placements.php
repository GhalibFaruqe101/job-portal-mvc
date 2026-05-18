<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/AnalyticsModel.php';

$model = new AnalyticsModel($conn);
$recruiter_id = $_SESSION['user_id'];

// Get all placements
$placements = $model->getPlacements($recruiter_id, 500); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement History - JobPortal Recruiter</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/analytics.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/recruiter_nav.php'; ?>

<main class="analytics-main">
    <div class="page-header">
        <div>
            <h1>Placement History</h1>
            <p>A complete log of all candidates you have successfully hired for clients.</p>
        </div>
        <div class="header-actions">
            <a href="analytics.php" class="btn-outline">&larr; Back to Analytics</a>
        </div>
    </div>

    <div class="analytics-card">
        <?php if (empty($placements)): ?>
            <div class="empty-state">
                <div class="empty-icon">📉</div>
                <p>No placements recorded yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Email</th>
                            <th>Job Title</th>
                            <th>Client / Company</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($placements as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['seeker_name']); ?></strong></td>
                                <td class="text-muted"><?php echo htmlspecialchars($p['seeker_email']); ?></td>
                                <td><?php echo htmlspecialchars($p['job_title']); ?></td>
                                <td>
                                    <span class="client-badge"><?php echo htmlspecialchars($p['client_name']); ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($p['applied_at'])); ?></td>
                                <td><span class="badge-success">Hired</span></td>
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

