<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=create">Post a Job</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>All Job Postings</h1>
                <a href="../controllers/JobController.php?action=create" class="btn-primary" style="text-decoration: none;">+ Post New Job</a>
            </div>

            <?php if (empty($jobs)): ?>
                <p style="margin-top: 2rem;">You haven't posted any jobs yet.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Title</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Applications</th>
                            <th style="padding: 1rem;">Posted On</th>
                            <th style="padding: 1rem;">Deadline</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($job['title']); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?php echo $job['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php if ($job['app_count'] > 0): ?>
                                        <a href="../controllers/ApplicationController.php?action=job_applications&job_id=<?php echo $job['id']; ?>" style="font-weight: bold; text-decoration: none;">
                                            <?php echo $job['app_count']; ?> &rarr;
                                        </a>
                                    <?php else: ?>
                                        <strong>0</strong>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                                <td style="padding: 1rem;"><?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : 'N/A'; ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/JobController.php?action=analytics&id=<?php echo $job['id']; ?>" style="color: #007bff; text-decoration: none;">Analytics</a> | 
                                    <a href="../controllers/JobController.php?action=edit&id=<?php echo $job['id']; ?>" style="text-decoration: none;">Edit</a> | 
                                    <a href="../controllers/JobController.php?action=delete&id=<?php echo $job['id']; ?>" style="color: red; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
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
