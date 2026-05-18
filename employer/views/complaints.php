<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - Job Portal</title>
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
            <a href="../controllers/RecruiterController.php?action=index">Recruiters</a>
            <a href="../controllers/ComplaintController.php?action=index">Complaints</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>My Complaints</h1>
            <p>View the status of complaints you have submitted to the platform administrators.</p>

            <?php if (empty($complaints)): ?>
                <p style="margin-top: 2rem; color: #666;">You have not submitted any complaints.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Reported User</th>
                            <th style="padding: 1rem;">Description</th>
                            <th style="padding: 1rem;">Date Submitted</th>
                            <th style="padding: 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($complaint['subject_name']); ?></strong><br>
                                    <span style="font-size: 0.85rem; color: #666;"><?php echo ucfirst($complaint['subject_role']); ?></span>
                                </td>
                                <td style="padding: 1rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($complaint['description']); ?>">
                                    <?php echo htmlspecialchars($complaint['description']); ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?php echo $complaint['status'] === 'resolved' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($complaint['status']); ?>
                                    </span>
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
