<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User - Job Portal</title>
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
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h1>Submit a Complaint</h1>
            <p>Report an issue regarding a <?php echo htmlspecialchars($subject['role']); ?> on the platform.</p>
            
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

            <?php if (empty($success)): ?>
                <form method="POST" action="?action=submit">
                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">
                    
                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Reporting:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($subject['name']); ?> (<?php echo ucfirst($subject['role']); ?>)" disabled>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Description of Issue *</label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Please provide details about the issue..." required></textarea>
                    </div>

                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <button type="submit" class="btn-primary" style="flex: 1; background-color: #dc3545;">Submit Report</button>
                        <a href="javascript:history.back()" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none;">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <div style="margin-top: 2rem;">
                    <a href="javascript:history.back()" class="btn-secondary" style="text-decoration: none;">&larr; Go Back</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
