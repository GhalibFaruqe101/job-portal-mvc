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
            <a href="../controllers/JobController.php?action=list">Manage Jobs</a>
            <a href="../controllers/ApplicationController.php?action=shortlisted">Shortlisted</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>Shortlisted Candidates</h1>
            <p>A unified list of all candidates you have shortlisted across all your job postings.</p>

            <?php if (empty($candidates)): ?>
                <p style="margin-top: 2rem;">You haven't shortlisted any candidates yet.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Applicant Name</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Applied For (Job)</th>
                            <th style="padding: 1rem;">Applied On</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $cand): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($cand['seeker_name']); ?></strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($cand['seeker_email']); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ApplicationController.php?action=job_applications&job_id=<?php echo $cand['job_id']; ?>" style="text-decoration: none;">
                                        <?php echo htmlspecialchars($cand['job_title']); ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($cand['applied_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="?action=view_applicant&id=<?php echo $cand['application_id']; ?>" class="btn-primary" style="padding: 0.3rem 0.6rem; text-decoration: none; font-size: 0.9rem;">View Profile & Message</a>
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
