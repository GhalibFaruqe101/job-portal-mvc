<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports & Complaints - Job Portal</title>
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
            <h1>Platform Abuse & Grievance Logs</h1>
            <p>Track submitted complaints and formal reports regarding seekers or recruiters. Administrative reviews are logged here.</p>

            <?php if (empty($complaints)): ?>
                <div style="margin-top: 2rem; background-color: #f8f9fa; padding: 2.5rem; text-align: center; border-radius: 4px;">
                    <p style="color: #6c757d; font-size: 1.1rem; font-weight: 500;">You have not filed any formal complaints yet.</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Target Party</th>
                            <th style="padding: 1rem;">Role</th>
                            <th style="padding: 1rem;">Report Description</th>
                            <th style="padding: 1rem;">Date Logged</th>
                            <th style="padding: 1rem;">Review Status</th>
                            <th style="padding: 1rem;">Admin Actions / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $comp): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem; font-weight: bold;"><?php echo htmlspecialchars($comp['subject_name']); ?></td>
                                <td style="padding: 1rem;"><span style="background-color: #e9ecef; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem; font-weight: bold;"><?php echo ucfirst($comp['subject_role']); ?></span></td>
                                <td style="padding: 1rem; max-width: 300px;"><?php echo htmlspecialchars($comp['description']); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($comp['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?php echo $comp['status'] === 'resolved' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($comp['status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; font-style: italic; color: #6c757d;">
                                    <?php echo $comp['admin_note'] ? htmlspecialchars($comp['admin_note']) : 'Awaiting admin review...'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
