<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Complaint - Job Portal</title>
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
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h1>File Formal Platform Report</h1>
            <p>Report candidate misconduct or abusive recruitment agencies.</p>

            <?php if (isset($success)): ?>
                <div
                    style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($success); ?>
                    <div style="margin-top: 1rem;">
                        <a href="../controllers/ComplaintController.php?action=index" class="btn-primary"
                            style="text-decoration: none; padding: 0.5rem 1rem;">View My Complaints</a>
                    </div>
                </div>
            <?php else: ?>

                <?php if (isset($error)): ?>
                    <div
                        style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div
                    style="background-color: #f8f9fa; padding: 1.25rem; border-radius: 4px; border-left: 4px solid #dc3545; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                    <p><strong>Reporting Target:</strong> <?php echo htmlspecialchars($subject['name']); ?></p>
                    <p style="margin-top: 0.25rem;"><strong>Platform Role:</strong>
                        <?php echo ucfirst(htmlspecialchars($subject['role'])); ?></p>
                </div>

                <form action="../controllers/ComplaintController.php?action=submit" method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Detailed Grievance
                            Description *</label>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 0.5rem;">Please provide a clear
                            chronological timeline of events, including any abusive messaging, contract breeches, or
                            fraudulent representations.</p>
                        <textarea name="description" rows="6" required
                            placeholder="Describe the misconduct or issue in detail..."
                            style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; resize: vertical; font-family: inherit;"></textarea>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn-primary"
                            style="background-color: #dc3545; border: none; padding: 0.75rem 2rem; cursor: pointer; border-radius: 4px; font-weight: bold; color: white;">Submit
                            Formal Complaint</button>
                        <a href="dashboard.php"
                            style="padding: 0.75rem 1.5rem; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; color: #333; text-align: center;">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>