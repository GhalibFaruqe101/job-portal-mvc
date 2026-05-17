<?php
require_once '../helpers/session.php';
require_role('recruiter'); // Secure the page for recruiters only
require_once '../config/db.php';
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

    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal <span style="font-size: 0.8rem; color: #8b5cf6;">[Recruiter]</span></a>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="clients.php">Clients</a>
            <a href="candidates.php">Candidates</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h1>
                <p>Here's what's happening with your talent pipeline today.</p>
            </div>
            <button class="btn-primary" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">+ New Placement</button>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">👥</div>
                <div class="stat-info">
                    <h3>124</h3>
                    <p>Total Candidates</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">🏢</div>
                <div class="stat-info">
                    <h3>12</h3>
                    <p>Active Clients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">⏳</div>
                <div class="stat-info">
                    <h3>28</h3>
                    <p>Pending Interviews</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">🏆</div>
                <div class="stat-info">
                    <h3>5</h3>
                    <p>Placements this Month</p>
                </div>
            </div>
        </section>

        <div class="dashboard-content">
            <div class="card recent-activity">
                <h2>Recent Activity</h2>
                <ul class="activity-list">
                    <li>
                        <div class="activity-dot blue"></div>
                        <div class="activity-text">
                            <strong>Alice Johnson</strong> accepted interview invitation for <em>Senior Developer</em> at TechCorp.
                            <span class="time">2 hours ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-dot green"></div>
                        <div class="activity-text">
                            <strong>Michael Smith</strong> was placed successfully at DataWorks.
                            <span class="time">5 hours ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-dot yellow"></div>
                        <div class="activity-text">
                            Client <strong>GlobalNet</strong> opened 3 new positions.
                            <span class="time">1 day ago</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="card quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <button class="action-btn">Search Candidates</button>
                    <button class="action-btn">Message Client</button>
                    <button class="action-btn">View Pipeline</button>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
