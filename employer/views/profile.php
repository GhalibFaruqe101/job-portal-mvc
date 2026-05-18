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
            <a href="../views/dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=create">Post a Job</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <h1>Company Profile</h1>
            <p>Update your company details and contact information.</p>

            <?php if (!empty($error))
                echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (!empty($success))
                echo "<p style='color:green;'>$success</p>"; ?>

            <form method="POST" action="?action=update">
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Company Name *</label>
                    <input type="text" name="company_name" class="form-control"
                        value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Industry</label>
                        <input type="text" name="industry" class="form-control"
                            value="<?php echo htmlspecialchars($profile['industry'] ?? ''); ?>"
                            placeholder="e.g. Technology, Healthcare">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Company Size</label>
                        <select name="company_size" class="form-control">
                            <option value="">Select Size</option>
                            <option value="1-10" <?php echo ($profile['company_size'] ?? '') === '1-10' ? 'selected' : ''; ?>>1-10 employees</option>
                            <option value="11-50" <?php echo ($profile['company_size'] ?? '') === '11-50' ? 'selected' : ''; ?>>11-50 employees</option>
                            <option value="51-200" <?php echo ($profile['company_size'] ?? '') === '51-200' ? 'selected' : ''; ?>>51-200 employees</option>
                            <option value="201-500" <?php echo ($profile['company_size'] ?? '') === '201-500' ? 'selected' : ''; ?>>201-500 employees</option>
                            <option value="500+" <?php echo ($profile['company_size'] ?? '') === '500+' ? 'selected' : ''; ?>>500+ employees</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        rows="5"><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Website</label>
                    <input type="url" name="website" class="form-control"
                        value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Address</label>
                    <textarea name="address" class="form-control"
                        rows="3"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                </div>

                <hr style="margin: 2rem 0;">
                <h3>Account Information</h3>
                <p style="font-size: 0.9rem; color: #666;">This information is tied to your login account and is
                    currently managed via the settings panel.</p>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Contact Name</label>
                        <input type="text" class="form-control"
                            value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" disabled>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Email Address</label>
                        <input type="email" class="form-control"
                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>