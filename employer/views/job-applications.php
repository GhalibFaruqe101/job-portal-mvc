<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applicants - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=create">Post a Job</a>
            <a href="../controllers/ApplicationController.php?action=shortlisted">Shortlisted</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/RecruiterController.php?action=index">Recruiters</a>
            <a href="../controllers/ComplaintController.php?action=index">Complaints</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>Candidates for: <?php echo htmlspecialchars($job['title']); ?></h1>
            <p>Review submitted candidate resumes and update candidate hiring stages via real-time status toggles.</p>

            <?php if (empty($applications)): ?>
                <div style="margin-top: 2rem; background-color: #f8f9fa; padding: 2rem; text-align: center; border-radius: 4px;">
                    <p style="color: #6c757d; font-size: 1.1rem;">No applications received for this job yet.</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Applicant Name</th>
                            <th style="padding: 1rem;">Email Address</th>
                            <th style="padding: 1rem;">Applied On</th>
                            <th style="padding: 1rem;">Hiring Status (AJAX Dropdown)</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr style="border-bottom: 1px solid #eee;" id="row-<?php echo $app['id']; ?>">
                                <td style="padding: 1rem; font-weight: bold;"><?php echo htmlspecialchars($app['seeker_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($app['seeker_email']); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <select onchange="updateStatus(<?php echo $app['id']; ?>, this)" style="padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc; font-weight: 500;">
                                        <option value="submitted" <?php echo $app['status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                        <option value="reviewed" <?php echo $app['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="shortlisted" <?php echo $app['status'] === 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                        <option value="interview" <?php echo $app['status'] === 'interview' ? 'selected' : ''; ?>>Interviewing</option>
                                        <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <span id="status-indicator-<?php echo $app['id']; ?>" style="margin-left: 0.5rem; transition: opacity 0.3s; opacity: 0; font-size: 0.85rem; font-weight: bold;"></span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ApplicationController.php?action=view_applicant&id=<?php echo $app['id']; ?>" class="btn-primary" style="text-decoration: none; padding: 0.4rem 0.8rem; font-size: 0.9rem;">View Profile & Chat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
    <script src="../../public/js/employer/status-update.js"></script>
</body>
</html>
