<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a New Job - Job Portal</title>
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
            <h1>Post a New Job</h1>

            <?php if (!empty($error))
                echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (!empty($success))
                echo "<p style='color:green;'>$success</p>"; ?>

            <form method="POST" action="?action=create">
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Job Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Job Type *</label>
                        <select name="job_type" class="form-control" required>
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                            <option value="remote">Remote</option>
                            <option value="contract">Contract</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Experience Level *</label>
                        <select name="experience_level" class="form-control" required>
                            <option value="entry">Entry Level</option>
                            <option value="mid">Mid Level</option>
                            <option value="senior">Senior Level</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" placeholder="City, State or Remote">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Minimum Salary</label>
                        <input type="number" name="salary_min" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Maximum Salary</label>
                        <input type="number" name="salary_max" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Application Deadline</label>
                    <input type="date" name="deadline" class="form-control">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Job Description *</label>
                    <textarea name="description" class="form-control" rows="5" required></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Requirements</label>
                    <textarea name="requirements" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Benefits</label>
                    <textarea name="benefits" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Publish Immediately (Active)</option>
                        <option value="draft">Save as Draft</option>
                    </select>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">Create Job Posting</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>