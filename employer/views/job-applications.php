<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications for <?php echo htmlspecialchars($job['title']); ?> - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
    <script>
        function updateStatus(applicationId, selectElement) {
            const status = selectElement.value;
            const originalColor = selectElement.style.backgroundColor;
            
            selectElement.style.opacity = '0.5';

            fetch('?action=update_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `application_id=${applicationId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                selectElement.style.opacity = '1';
                if(data.success) {
                    // Flash green to indicate success
                    selectElement.style.backgroundColor = '#d4edda';
                    setTimeout(() => selectElement.style.backgroundColor = originalColor, 1000);
                } else {
                    alert('Failed to update status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                selectElement.style.opacity = '1';
                alert('Network error occurred');
            });
        }
    </script>
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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Applications for "<?php echo htmlspecialchars($job['title']); ?>"</h1>
                <a href="../controllers/JobController.php?action=list" class="btn-secondary" style="text-decoration: none;">&larr; Back to Jobs</a>
            </div>

            <?php if (empty($applications)): ?>
                <p style="margin-top: 2rem;">No one has applied to this job yet.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Applicant Name</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Applied On</th>
                            <th style="padding: 1rem;">Status (Auto-Saves)</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($app['seeker_name']); ?></strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($app['seeker_email']); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <select class="form-control" style="width: auto; padding: 0.2rem;" onchange="updateStatus(<?php echo $app['id']; ?>, this)">
                                        <option value="submitted" <?php echo $app['status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                        <option value="reviewed" <?php echo $app['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="shortlisted" <?php echo $app['status'] === 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                        <option value="interview" <?php echo $app['status'] === 'interview' ? 'selected' : ''; ?>>Interview</option>
                                        <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="?action=view_applicant&id=<?php echo $app['id']; ?>" class="btn-primary" style="padding: 0.3rem 0.6rem; text-decoration: none; font-size: 0.9rem;">View Profile</a>
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
