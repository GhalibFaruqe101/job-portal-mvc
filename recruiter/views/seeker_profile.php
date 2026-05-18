<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/SeekerModel.php';
require_once '../models/JobModel.php';

$seeker_id = (int)($_GET['id'] ?? 0);
if (!$seeker_id) {
    header("Location: seekers.php");
    exit();
}

$seekerModel = new SeekerModel($conn);
$seeker = $seekerModel->getSeekerProfile($seeker_id);

if (!$seeker) {
    header("Location: seekers.php");
    exit();
}

$jobModel = new JobModel($conn);
$recruiter_id = $_SESSION['user_id'];
// Get active jobs for this recruiter to populate the outreach dropdown
$activeJobs = $jobModel->getRecruiterJobs($recruiter_id, '', 'active');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seeker['name']); ?> - Candidate Profile</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/seekers.css">
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
        <a href="candidates.php">Candidates</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="seekers-main">
    <div class="page-header">
        <div>
            <h1>Candidate Profile</h1>
            <p>Review candidate details and reach out.</p>
        </div>
        <a href="seekers.php" class="btn-back">← Back to Search</a>
    </div>

    <div class="profile-layout">
        <!-- Profile Info -->
        <div class="profile-card">
            <div class="profile-header-card">
                <div class="profile-avatar-large"><?php echo strtoupper(substr($seeker['name'], 0, 1)); ?></div>
                <div class="profile-title">
                    <h2><?php echo htmlspecialchars($seeker['name']); ?></h2>
                    <p><?php echo htmlspecialchars($seeker['headline'] ?? 'No headline provided'); ?></p>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-section">
                    <h3>About</h3>
                    <p><?php echo nl2br(htmlspecialchars($seeker['summary'] ?? 'No summary provided.')); ?></p>
                </div>

                <div class="detail-section">
                    <h3>Skills</h3>
                    <div class="seeker-skills">
                        <?php 
                        $skills = explode(',', $seeker['skills'] ?? '');
                        foreach ($skills as $skill) {
                            if (trim($skill)) echo '<span class="skill-tag">' . htmlspecialchars(trim($skill)) . '</span>';
                        }
                        ?>
                    </div>
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Experience</span>
                        <span class="detail-value"><?php echo (int)$seeker['years_experience']; ?> Years</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Education</span>
                        <span class="detail-value"><?php echo htmlspecialchars($seeker['education_level'] ?? 'Not specified'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Expected Salary</span>
                        <span class="detail-value"><?php echo $seeker['expected_salary'] ? '৳' . number_format($seeker['expected_salary']) : 'Negotiable'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Location</span>
                        <span class="detail-value"><?php echo htmlspecialchars($seeker['preferred_location'] ?? 'Anywhere'); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($seeker['resume_path'])): ?>
                <div class="resume-section">
                    <h3>Resume</h3>
                    <a href="../../<?php echo htmlspecialchars($seeker['resume_path']); ?>" target="_blank" class="btn-download">📄 Download Resume</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Outreach Box -->
        <div class="outreach-card">
            <h3>Reach Out</h3>
            <p>Send a direct message regarding an active job.</p>
            
            <div id="outreach-alert" style="display:none; margin-bottom:1rem; padding:1rem; border-radius:8px;"></div>

            <form id="outreachForm">
                <input type="hidden" id="seeker_id" value="<?php echo $seeker['seeker_id']; ?>">
                
                <div class="form-group">
                    <label for="job_id">Select Job Opportunity</label>
                    <select id="job_id" class="form-control" required>
                        <option value="">Choose a job...</option>
                        <?php foreach ($activeJobs as $job): ?>
                            <option value="<?php echo $job['id']; ?>">
                                <?php echo htmlspecialchars($job['title']); ?> (<?php echo htmlspecialchars($job['client_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" class="form-control" rows="5" required placeholder="Hi <?php echo htmlspecialchars(explode(' ', $seeker['name'])[0]); ?>..."></textarea>
                </div>

                <button type="submit" class="btn-send-outreach" id="btnSend">Send Message</button>
            </form>
        </div>
    </div>
</main>

<script>
document.getElementById('outreachForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSend');
    const alertBox = document.getElementById('outreach-alert');
    
    const seekerId = document.getElementById('seeker_id').value;
    const jobId = document.getElementById('job_id').value;
    const message = document.getElementById('message').value;

    btn.disabled = true;
    btn.textContent = 'Sending...';
    
    const formData = new FormData();
    formData.append('seeker_id', seekerId);
    formData.append('job_id', jobId);
    formData.append('message', message);
    formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');

    fetch('../api/send_outreach.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alertBox.style.display = 'block';
        if(data.success) {
            alertBox.className = 'alert-success';
            alertBox.innerHTML = '✅ Message sent successfully!';
            document.getElementById('outreachForm').reset();
        } else {
            alertBox.className = 'alert-danger';
            alertBox.innerHTML = '⚠️ ' + data.error;
            btn.disabled = false;
            btn.textContent = 'Send Message';
        }
    })
    .catch(err => {
        alertBox.style.display = 'block';
        alertBox.className = 'alert-danger';
        alertBox.innerHTML = '⚠️ A network error occurred.';
        btn.disabled = false;
        btn.textContent = 'Send Message';
    });
});
</script>

</body>
</html>
