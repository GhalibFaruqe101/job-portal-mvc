<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/JobModel.php';
require_once '../models/ClientModel.php';

$jobModel    = new JobModel($conn);
$clientModel = new ClientModel($conn);
$recruiter_id = $_SESSION['user_id'];

// Flash messages
$success = $_SESSION['job_success'] ?? '';
$errors  = $_SESSION['job_errors']  ?? [];
unset($_SESSION['job_success'], $_SESSION['job_errors']);

// Filters
$f_client   = $_GET['client']   ?? '';
$f_status   = $_GET['status']   ?? '';
$f_category = $_GET['category'] ?? '';

$jobs       = $jobModel->getRecruiterJobs($recruiter_id, $f_client, $f_status, $f_category);
$categories = $jobModel->getCategories();
$clients    = $clientModel->getMyClients($recruiter_id);

$statusLabels = ['active' => 'Active', 'closed' => 'Closed', 'draft' => 'Draft'];
$jobTypes     = ['full-time' => 'Full-Time', 'part-time' => 'Part-Time', 'remote' => 'Remote', 'contract' => 'Contract'];
$expLevels    = ['entry' => 'Entry', 'mid' => 'Mid', 'senior' => 'Senior'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs - JobPortal Recruiter</title>
    <meta name="description" content="Manage job postings for your clients.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/jobs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="global-nav">
    <a href="dashboard.php" class="logo">JobPortal <span style="font-size:0.8rem;color:#8b5cf6;">[Recruiter]</span></a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php">Clients</a>
        <a href="jobs.php" class="active">Jobs</a>
        <a href="candidates.php">Candidates</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="jobs-main">
    <div class="page-header">
        <div>
            <h1>Job Postings</h1>
            <p>Create and manage jobs on behalf of your clients.</p>
        </div>
        <a href="job_form.php" class="btn-post-job">+ Post New Job</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><span>✅</span> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><span>⚠️</span> <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <form method="GET" action="jobs.php" class="filter-bar">
        <select name="client" onchange="this.form.submit()">
            <option value="">All Clients</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?php echo $c['client_id']; ?>" <?php echo ($f_client == $c['client_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['company_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach ($statusLabels as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo ($f_status === $k) ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($f_category == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Job Cards -->
    <div class="jobs-grid">
        <?php if (empty($jobs)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>No jobs posted yet.</p>
                <a href="job_form.php" style="color:#7c3aed;font-weight:600;">Post your first job →</a>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $j): ?>
                <div class="job-card">
                    <div class="job-card-header">
                        <div>
                            <h2><?php echo htmlspecialchars($j['title']); ?></h2>
                            <p class="job-client">🏢 <?php echo htmlspecialchars($j['client_name']); ?></p>
                        </div>
                        <span class="status-badge status-<?php echo htmlspecialchars($j['status'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($statusLabels[$j['status']] ?? ucfirst($j['status']), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                    <div class="job-meta">
                        <?php if ($j['location']): ?>
                            <span>📍 <?php echo htmlspecialchars($j['location']); ?></span>
                        <?php endif; ?>
                        <span>💼 <?php echo $jobTypes[$j['job_type']] ?? htmlspecialchars($j['job_type'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span>📊 <?php echo $expLevels[$j['experience_level']] ?? htmlspecialchars($j['experience_level'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($j['category_name']): ?>
                            <span>🏷️ <?php echo htmlspecialchars($j['category_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($j['salary_min'] || $j['salary_max']): ?>
                        <div class="job-salary">
                            💰 ৳<?php echo number_format($j['salary_min']); ?> – ৳<?php echo number_format($j['salary_max']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="job-card-footer">
                        <div class="job-stats">
                            <span class="app-count"><?php echo $j['app_count']; ?> Application<?php echo $j['app_count'] != 1 ? 's' : ''; ?></span>
                            <?php if ($j['deadline']): ?>
                                <span class="deadline">⏰ <?php echo date('d M Y', strtotime($j['deadline'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="job-actions">
                            <a href="job_form.php?id=<?php echo $j['id']; ?>" class="btn-edit">Edit</a>
                            <form method="POST" action="../controllers/JobController.php?action=delete"
                                  onsubmit="return confirm('Delete this job?');" style="display:inline;">
                                <input type="hidden" name="job_id" value="<?php echo $j['id']; ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
