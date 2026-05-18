<?php
require_once '../helpers/session.php';
require_role('recruiter'); // Secure the page for recruiters only
require_once '../config/db.php';
require_once '../models/AnalyticsModel.php';
require_once '../models/CandidateModel.php';

$recruiter_id = $_SESSION['user_id'];
$analyticsModel = new AnalyticsModel($conn);
$stats = $analyticsModel->getOverviewStats($recruiter_id);

// Recent activity: latest applications for recruiter's jobs
$candModel = new CandidateModel($conn);
$recentActivities = $candModel->getRecruiterCandidates($recruiter_id, '', '', '', 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard - JobPortal</title>
    <meta name="description" content="Recruiter Dashboard - JobPortal">
    
    <!-- 1. Shared Global CSS -->
    <link rel="stylesheet" href="../../public/css/style.css">
    <!-- 2. Specific Recruiter Dashboard CSS -->
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'partials/recruiter_nav.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h1>
                <p>Here's what's happening with your talent pipeline today.</p>
            </div>
            <a href="candidates.php" class="btn-primary" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; color: white; display: inline-block; font-weight: 600;">+ New Placement</a>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">👥</div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_candidates']; ?></h3>
                    <p>Total Candidates</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">🏢</div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_clients']; ?></h3>
                    <p>Active Clients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">💼</div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_jobs']; ?></h3>
                    <p>Active Jobs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">🏆</div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_hired']; ?></h3>
                    <p>Successful Hires</p>
                </div>
            </div>
        </section>

        <div class="dashboard-content">
            <div class="card recent-activity">
                <h2>Recent Activity</h2>
                <ul class="activity-list">
                    <?php if (empty($recentActivities)): ?>
                        <li>
                            <div class="activity-dot blue"></div>
                            <div class="activity-text">
                                No recent activity yet. Start by posting jobs and managing candidates!
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $a): ?>
                            <?php
                                $dotColor = 'blue';
                                if ($a['app_status'] === 'hired') $dotColor = 'green';
                                elseif ($a['app_status'] === 'interview') $dotColor = 'yellow';
                                elseif ($a['app_status'] === 'rejected') $dotColor = 'red';
                                
                                $timeAgo = '';
                                $diff = time() - strtotime($a['applied_at']);
                                if ($diff < 3600) $timeAgo = floor($diff / 60) . ' min ago';
                                elseif ($diff < 86400) $timeAgo = floor($diff / 3600) . ' hours ago';
                                else $timeAgo = floor($diff / 86400) . ' days ago';
                            ?>
                            <li>
                                <div class="activity-dot <?php echo $dotColor; ?>"></div>
                                <div class="activity-text">
                                    <strong><?php echo htmlspecialchars($a['seeker_name']); ?></strong>
                                    applied for <em><?php echo htmlspecialchars($a['job_title']); ?></em>
                                    — Status: <?php echo htmlspecialchars(ucfirst($a['app_status'])); ?>
                                    <span class="time"><?php echo $timeAgo; ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="candidates.php" class="action-btn" style="text-decoration: none; display: block;">Search Candidates</a>
                    <a href="clients.php" class="action-btn" style="text-decoration: none; display: block;">Message Client</a>
                    <a href="analytics.php" class="action-btn" style="text-decoration: none; display: block;">View Analytics</a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>

