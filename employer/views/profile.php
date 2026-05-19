<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - Job Portal</title>
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
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <h1>Manage Company Profile</h1>
            <p>Update your company details visible to prospective job seekers.</p>

            <?php if (isset($success)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="../controllers/ProfileController.php?action=update" method="POST" style="margin-top: 2rem;">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Company Name *</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? $user['name']); ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Industry</label>
                        <input type="text" name="industry" value="<?php echo htmlspecialchars($profile['industry'] ?? ''); ?>" placeholder="e.g. Technology, Health" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Company Size</label>
                        <select name="company_size" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="">Select Company Size</option>
                            <option value="1-10" <?php echo ($profile['company_size'] ?? '') === '1-10' ? 'selected' : ''; ?>>1-10 employees</option>
                            <option value="11-50" <?php echo ($profile['company_size'] ?? '') === '11-50' ? 'selected' : ''; ?>>11-50 employees</option>
                            <option value="51-200" <?php echo ($profile['company_size'] ?? '') === '51-200' ? 'selected' : ''; ?>>51-200 employees</option>
                            <option value="201-500" <?php echo ($profile['company_size'] ?? '') === '201-500' ? 'selected' : ''; ?>>201-500 employees</option>
                            <option value="500+" <?php echo ($profile['company_size'] ?? '') === '500+' ? 'selected' : ''; ?>>500+ employees</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Company Website</label>
                    <input type="url" name="website" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>" placeholder="https://example.com" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Company Description</label>
                    <textarea name="description" rows="5" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Office Address</label>
                    <textarea name="address" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem; cursor: pointer; border: none; border-radius: 4px;">Save Changes</button>
            </form>
            
            <hr style="margin: 3rem 0; border: none; border-top: 1px solid #eee;">

            <h2>Account Security Credentials</h2>
            <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 4px; margin-top: 1rem;">
                <p><strong>Registered Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Primary Phone:</strong> <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'N/A'; ?></p>
                <p style="color: #6c757d; font-size: 0.9rem; margin-top: 1rem;">Note: Account profile credentials are set during registration and managed via security admins.</p>
            </div>
        </div>
    </main>
</body>
</html>
