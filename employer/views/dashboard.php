<?php
require_once '../helpers/session.php';
require_role('employer'); // Secures the page!
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=create">Post a Job</a>
            <a href="profile.php">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>Employer Dashboard</h1>
            <p>Welcome, Employer #<?php echo htmlspecialchars($_SESSION['user_id']); ?>!</p>
            
            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Total Jobs</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;">0</p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Active Jobs</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;">0</p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Total Applications</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;">0</p>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h2>Recent Job Postings</h2>
                <p>No jobs posted yet. <a href="../controllers/JobController.php?action=create">Post a new job</a>.</p>
            </div>
        </div>
    </main>
</body>
</html>
