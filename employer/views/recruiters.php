<?php
require_once '../helpers/session.php';
require_role('employer');
?>
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
            <h1>Authorised Recruitment Agencies</h1>
            <p>View and manage recruitment agencies authorized to publish job listings or source candidates on behalf of your company.</p>

            <?php if (empty($recruiters)): ?>
                <div style="margin-top: 2rem; background-color: #f8f9fa; padding: 2.5rem; text-align: center; border-radius: 4px;">
                    <p style="color: #6c757d; font-size: 1.1rem; font-weight: 500;">No recruitment agencies are currently linked to your company.</p>
                    <p style="color: #8c959d; font-size: 0.95rem; margin-top: 0.5rem;">Authorise external recruiters through the partner integrations system.</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 1rem;">Agency Name</th>
                            <th style="padding: 1rem;">Specialisation</th>
                            <th style="padding: 1rem;">Recruiter Name</th>
                            <th style="padding: 1rem;">Contact Info</th>
                            <th style="padding: 1rem;">Authorised Since</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recruiters as $rec): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;">
                                    <div style="font-weight: bold;"><?php echo htmlspecialchars($rec['agency_name'] ?? 'Freelance Recruiter'); ?></div>
                                    <?php if ($rec['website']): ?>
                                        <a href="<?php echo htmlspecialchars($rec['website']); ?>" target="_blank" style="font-size: 0.8rem; color: #6c757d; text-decoration: none;">Visit Website &rarr;</a>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo $rec['specialization'] ? htmlspecialchars($rec['specialization']) : 'General'; ?></td>
                                <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($rec['name']); ?></td>
                                <td style="padding: 1rem;">
                                    <p style="margin: 0;"><strong>Email:</strong> <?php echo htmlspecialchars($rec['email']); ?></p>
                                    <p style="margin: 0;"><strong>Phone:</strong> <?php echo $rec['phone'] ? htmlspecialchars($rec['phone']) : 'N/A'; ?></p>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($rec['added_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="../controllers/ComplaintController.php?action=create&subject_id=<?php echo $rec['recruiter_id']; ?>" class="btn-primary" style="background-color: #dc3545; color: white; text-decoration: none; padding: 0.4rem 0.8rem; font-size: 0.9rem; border-radius: 4px;">File Complaint</a>
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
