<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Agencies - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=list">Manage Jobs</a>
            <a href="../controllers/RecruiterController.php?action=index">Recruiters</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>Recruiter Agencies</h1>
            <p>Manage the recruitment agencies that have permission to post jobs on your behalf.</p>

            <?php if (empty($recruiters)): ?>
                <p style="margin-top: 2rem; color: #666;">There are no recruiter agencies currently linked to your company.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Agency Name</th>
                            <th style="padding: 1rem;">Contact Person</th>
                            <th style="padding: 1rem;">Specialization</th>
                            <th style="padding: 1rem;">Linked On</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recruiters as $recruiter): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($recruiter['agency_name'] ?? 'Agency Unnamed'); ?></strong>
                                    <?php if ($recruiter['website']): ?>
                                        <br><a href="<?php echo htmlspecialchars($recruiter['website']); ?>" target="_blank" style="font-size: 0.85rem;">Website</a>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php echo htmlspecialchars($recruiter['name']); ?><br>
                                    <span style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($recruiter['email']); ?></span>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($recruiter['specialization'] ?? 'General'); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($recruiter['added_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ComplaintController.php?action=create&subject_id=<?php echo $recruiter['recruiter_id']; ?>" style="color: red; text-decoration: none;">Report to Admin</a>
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
