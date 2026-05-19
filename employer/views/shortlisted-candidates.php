<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortlisted Candidates - Job Portal</title>
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
            <h1>Unified Shortlisted Candidates</h1>
            <p>Access and review top candidates who have been shortlisted across all your job postings in one central list.</p>

            <?php if (empty($candidates)): ?>
                <div style="margin-top: 2rem; background-color: #f8f9fa; padding: 2.5rem; text-align: center; border-radius: 4px;">
                    <p style="color: #6c757d; font-size: 1.1rem; font-weight: 500;">No candidates have been shortlisted yet.</p>
                    <p style="color: #8c959d; font-size: 0.95rem; margin-top: 0.5rem;">Shortlist applicants from the candidates page under individual job postings.</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Candidate Name</th>
                            <th style="padding: 1rem;">Email Address</th>
                            <th style="padding: 1rem;">Applied Job Posting</th>
                            <th style="padding: 1rem;">Shortlisted On</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $candidate): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem; font-weight: bold;"><?php echo htmlspecialchars($candidate['seeker_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($candidate['seeker_email']); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ApplicationController.php?action=job_applications&job_id=<?php echo $candidate['job_id']; ?>" style="text-decoration: none; font-weight: bold; color: #007bff;">
                                        <?php echo htmlspecialchars($candidate['job_title']); ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($candidate['applied_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ApplicationController.php?action=view_applicant&id=<?php echo $candidate['application_id']; ?>" class="btn-primary" style="text-decoration: none; padding: 0.4rem 0.8rem; font-size: 0.9rem;">View Profile & Chat</a>
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
