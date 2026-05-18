<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/CandidateModel.php';
require_once '../models/JobModel.php';

$model = new CandidateModel($conn);
$jobModel = new JobModel($conn);
$recruiter_id = $_SESSION['user_id'];

// Flash messages
$success = $_SESSION['candidate_success'] ?? '';
$error   = $_SESSION['candidate_error']   ?? '';
unset($_SESSION['candidate_success'], $_SESSION['candidate_error']);

// Filters from GET
$search        = trim($_GET['search'] ?? '');
$filter_status = $_GET['status'] ?? '';
$filter_job    = $_GET['job_id'] ?? '';

$jobs         = $jobModel->getRecruiterJobs($recruiter_id);
$candidates   = $model->getRecruiterCandidates($recruiter_id, $filter_job, $filter_status, $search);
$statusCounts = $model->getStatusCounts($recruiter_id);
$total        = array_sum($statusCounts);
$csrf_token   = generateCsrfToken();

$statusLabels = [
    'submitted'   => 'Submitted',
    'reviewed'    => 'Reviewed',
    'shortlisted' => 'Shortlisted',
    'interview'   => 'Interview',
    'rejected'    => 'Rejected',
    'withdrawn'   => 'Withdrawn',
    'hired'       => 'Hired',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates - JobPortal Recruiter</title>
    <meta name="description" content="Manage your talent pipeline and candidate applications.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/candidates.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="global-nav">
    <a href="dashboard.php" class="logo">JobPortal <span style="font-size:0.8rem;color:#8b5cf6;">[Recruiter]</span></a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php">Clients</a>
        <a href="jobs.php">Jobs</a>
        <a href="seekers.php">Seekers</a>
        <a href="outreach.php">Outreach</a>
        <a href="candidates.php" class="active">Candidates</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="candidates-main">

    <div class="page-header">
        <div>
            <h1>Candidate Pipeline</h1>
            <p>Track and manage all candidate applications across your client jobs.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><span>✅</span> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><span>⚠️</span> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Status Summary Chips -->
    <div class="status-bar">
        <a href="candidates.php" class="status-chip all <?php echo ($filter_status === '') ? 'active' : ''; ?>">
            All (<?php echo $total; ?>)
        </a>
        <?php foreach ($statusLabels as $key => $label): ?>
            <a href="candidates.php?status=<?php echo $key; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"
               class="status-chip <?php echo $key; ?> <?php echo ($filter_status === $key) ? 'active' : ''; ?>">
                <?php echo $label; ?> (<?php echo $statusCounts[$key] ?? 0; ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Search + Filter Bar -->
    <form method="GET" action="candidates.php">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
        <div class="filter-bar" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" id="search-input"
                   placeholder="Search by candidate name or job title..."
                   value="<?php echo htmlspecialchars($search); ?>">
            <select name="job_id" onchange="this.form.submit()">
                <option value="">All Jobs</option>
                <?php foreach ($jobs as $job): ?>
                    <option value="<?php echo $job['id']; ?>" <?php echo ($filter_job == $job['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($job['title']); ?> (<?php echo htmlspecialchars($job['client_name']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($filter_status === $key) ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-filter">Search</button>
        </div>
    </form>

    <!-- Candidates Table -->
    <div class="table-wrapper">
        <?php if (empty($candidates)): ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>No candidates found<?php echo $search ? ' for "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
            </div>
        <?php else: ?>
        <table class="candidates-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Job Applied For</th>
                    <th>Company</th>
                    <th>Applied On</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $c): ?>
                <tr id="row-<?php echo $c['application_id']; ?>">
                    <td data-label="Candidate">
                        <div class="candidate-cell">
                            <div class="candidate-avatar">
                                <?php if (!empty($c['profile_pic'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($c['profile_pic']); ?>" alt="">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($c['seeker_name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="candidate-name"><?php echo htmlspecialchars($c['seeker_name']); ?></div>
                                <div class="candidate-email"><?php echo htmlspecialchars($c['seeker_email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Job"><?php echo htmlspecialchars($c['job_title']); ?></td>
                    <td data-label="Company"><?php echo htmlspecialchars($c['company_name'] ?? '—'); ?></td>
                    <td data-label="Applied On"><?php echo date('d M Y', strtotime($c['applied_at'])); ?></td>
                    <td data-label="Status" id="badge-<?php echo $c['application_id']; ?>">
                        <span class="status-badge badge-<?php echo $c['app_status']; ?>">
                            <?php echo $statusLabels[$c['app_status']] ?? ucfirst($c['app_status']); ?>
                        </span>
                    </td>
                    <td data-label="Update">
                        <select class="status-select"
                                data-app-id="<?php echo $c['application_id']; ?>"
                                onchange="updateStatus(this)">
                            <?php foreach ($statusLabels as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo ($c['app_status'] === $key) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="saving-indicator" id="saving-<?php echo $c['application_id']; ?>">Saving…</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</main>

<script>
/**
 * AJAX status update — no page reload
 */
function updateStatus(selectEl) {
    const appId     = selectEl.dataset.appId;
    const newStatus = selectEl.value;
    const indicator = document.getElementById('saving-' + appId);
    const badge     = document.getElementById('badge-' + appId);

    indicator.style.display = 'inline';

    const formData = new FormData();
    formData.append('application_id', appId);
    formData.append('status', newStatus);
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');

    fetch('../api/update_candidate_status.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        indicator.style.display = 'none';
        if (data.success) {
            // Update badge label + class live
            const statusLabels = {
                submitted: 'Submitted', reviewed: 'Reviewed', shortlisted: 'Shortlisted',
                interview: 'Interview', rejected: 'Rejected',  withdrawn: 'Withdrawn', hired: 'Hired'
            };
            const span = document.createElement('span');
            span.className = 'status-badge badge-' + data.new_status;
            if (statusLabels[data.new_status]) {
                span.textContent = statusLabels[data.new_status];
            } else {
                span.textContent = data.new_status;
            }
            badge.textContent = '';
            badge.appendChild(span);
        } else {
            alert('Update failed: ' + data.message);
            // Revert to previous value would require storing it — just notify user
        }
    })
    .catch(() => {
        indicator.style.display = 'none';
        alert('Network error. Please try again.');
    });
}
</script>

</body>
</html>
