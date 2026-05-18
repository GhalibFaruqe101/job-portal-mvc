<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/OutreachModel.php';

$model = new OutreachModel($conn);
$recruiter_id = $_SESSION['user_id'];

$f_status = $_GET['status'] ?? '';

$messages = $model->getMyOutreach($recruiter_id, $f_status);
$stats = $model->getOutreachStats($recruiter_id);

$statusLabels = [
    'sent'      => 'Sent',
    'read'      => 'Read',
    'responded' => 'Responded'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outreach - JobPortal Recruiter</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/recruiter_base.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/outreach.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/recruiter_nav.php'; ?>

<main class="outreach-main">
    <div class="page-header">
        <div>
            <h1>Outreach Messages</h1>
            <p>Track the direct messages you've sent to candidates.</p>
        </div>
        <a href="seekers.php" class="btn-new-outreach">Find Candidates</a>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Sent</h3>
            <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
        </div>
        <div class="stat-card">
            <h3>Read</h3>
            <div class="stat-value text-blue"><?php echo $stats['read_count'] ?? 0; ?></div>
        </div>
        <div class="stat-card">
            <h3>Responded</h3>
            <div class="stat-value text-green"><?php echo $stats['responded_count'] ?? 0; ?></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="outreach.php" class="filter-bar">
        <select name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach ($statusLabels as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo ($f_status === $k) ? 'selected' : ''; ?>>
                    <?php echo $v; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="outreach-container">
        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <div class="empty-icon">📨</div>
                <p>No outreach messages found.</p>
                <a href="seekers.php" style="color:#7c3aed;font-weight:600;">Start searching for candidates →</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="outreach-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job Opportunity</th>
                            <th>Message Snippet</th>
                            <th>Sent At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td>
                                    <div class="candidate-info">
                                        <strong><?php echo htmlspecialchars($m['seeker_name']); ?></strong>
                                        <span><?php echo htmlspecialchars($m['seeker_email']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="job-info">
                                        <strong><?php echo htmlspecialchars($m['job_title']); ?></strong>
                                        <span><?php echo htmlspecialchars($m['client_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="message-snippet" title="<?php echo htmlspecialchars($m['message']); ?>">
                                        <?php 
                                            $snippet = mb_strlen($m['message'], 'UTF-8') > 50 ? mb_substr($m['message'], 0, 50, 'UTF-8') . '...' : $m['message'];
                                            echo htmlspecialchars($snippet);
                                        ?>
                                    </div>
                                </td>
                                <td class="date-col">
                                    <?php echo date('d M Y, h:i A', strtotime($m['sent_at'])); ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($m['status'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo $statusLabels[$m['status']] ?? ucfirst($m['status']); ?>
                                    </span>
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


