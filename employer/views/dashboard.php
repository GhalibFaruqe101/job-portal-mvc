<?php
require_once '../helpers/session.php';
require_role('employer'); // Secures the page!
require_once '../config/db.php';

$employer_id = $_SESSION['user_id'];

// Fetch Total Jobs
$stmt = $conn->prepare('SELECT COUNT(*) FROM jobs WHERE employer_id = ?');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$stmt->bind_result($total_jobs);
$stmt->fetch();
$stmt->close();

// Fetch Active Jobs
$stmt = $conn->prepare('SELECT COUNT(*) FROM jobs WHERE employer_id = ? AND status = "active"');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$stmt->bind_result($active_jobs);
$stmt->fetch();
$stmt->close();

// Fetch Total Applications for this employer's jobs
$stmt = $conn->prepare('SELECT COUNT(a.id) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ?');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$stmt->bind_result($total_applications);
$stmt->fetch();
$stmt->close();

// Fetch Recent Jobs
$recent_jobs = [];
$stmt = $conn->prepare('
    SELECT id, title, status, deadline, 
    (SELECT COUNT(*) FROM applications WHERE job_id = jobs.id) as app_count 
    FROM jobs 
    WHERE employer_id = ? 
    ORDER BY created_at DESC LIMIT 5
');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_jobs[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Job Portal</title>
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
            <h1>Employer Dashboard</h1>
            <p>Welcome, Employer #<?php echo htmlspecialchars($_SESSION['user_id']); ?>!</p>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Total Jobs</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_jobs; ?></p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Active Jobs</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;"><?php echo $active_jobs; ?></p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Total Applications</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0;"><?php echo $total_applications; ?>
                    </p>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h2>Recent Job Postings</h2>
                <?php if (empty($recent_jobs)): ?>
                    <p>No jobs posted yet. <a href="../controllers/JobController.php?action=create">Post a new job</a>.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                                <th style="padding: 0.5rem;">Title</th>
                                <th style="padding: 0.5rem;">Status</th>
                                <th style="padding: 0.5rem;">Applications</th>
                                <th style="padding: 0.5rem;">Deadline</th>
                                <th style="padding: 0.5rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_jobs as $job): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 0.5rem;"><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td style="padding: 0.5rem;">
                                        <span
                                            class="badge badge-<?php echo $job['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($job['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 0.5rem;"><?php echo $job['app_count']; ?></td>
                                    <td style="padding: 0.5rem;">
                                        <?php echo $job['deadline'] ? htmlspecialchars($job['deadline']) : 'N/A'; ?>
                                    </td>
                                    <td style="padding: 0.5rem;">
                                        <a
                                            href="../controllers/JobController.php?action=view&id=<?php echo $job['id']; ?>">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        <a href="../controllers/JobController.php?action=list">View All Jobs</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>