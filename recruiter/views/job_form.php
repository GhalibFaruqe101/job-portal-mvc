<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/JobModel.php';
require_once '../models/ClientModel.php';

$jobModel    = new JobModel($conn);
$clientModel = new ClientModel($conn);
$recruiter_id = $_SESSION['user_id'];

// Check if editing
$job_id  = (int)($_GET['id'] ?? 0);
$isEdit  = false;
$job     = null;

if ($job_id) {
    $job = $jobModel->getJobById($job_id, $recruiter_id);
    if ($job) {
        $isEdit = true;
    } else {
        header("Location: jobs.php");
        exit();
    }
}

// Flash data
$errors  = $_SESSION['job_errors'] ?? [];
$oldData = $_SESSION['old_job']    ?? [];
unset($_SESSION['job_errors'], $_SESSION['old_job']);

// Merge: old POST data > existing job > empty
$f = function($key) use ($oldData, $job) {
    return $oldData[$key] ?? ($job[$key] ?? '');
};

$categories = $jobModel->getCategories();
$clients    = $clientModel->getMyClients($recruiter_id);

$jobTypes  = ['full-time' => 'Full-Time', 'part-time' => 'Part-Time', 'remote' => 'Remote', 'contract' => 'Contract'];
$expLevels = ['entry' => 'Entry Level', 'mid' => 'Mid Level', 'senior' => 'Senior Level'];
$statuses  = ['active' => 'Active', 'draft' => 'Draft', 'closed' => 'Closed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Job' : 'Post New Job'; ?> - JobPortal Recruiter</title>
    <meta name="description" content="<?php echo $isEdit ? 'Edit a job posting.' : 'Create a new job posting for a client.'; ?>">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/recruiter_base.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/jobs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/recruiter_nav.php'; ?>

<main class="jobs-main">
    <div class="page-header">
        <div>
            <h1><?php echo $isEdit ? 'Edit Job' : 'Post New Job'; ?></h1>
            <p><?php echo $isEdit ? 'Update job details below.' : 'Fill in the details to create a new job posting for a client.'; ?></p>
        </div>
        <a href="jobs.php" class="btn-back">← Back to Jobs</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <div><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
        </div>
    <?php endif; ?>

    <?php if (empty($clients)): ?>
        <div class="alert alert-danger">
            <span>⚠️</span>
            <div>You need to <a href="clients.php" style="color:#7c3aed;font-weight:600;">add a client</a> before you can post jobs.</div>
        </div>
    <?php else: ?>

    <div class="job-form-card">
        <form action="../controllers/JobController.php?action=<?php echo $isEdit ? 'update' : 'create'; ?>" method="POST">
            <?php if ($isEdit): ?>
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
            <?php endif; ?>

            <div class="form-section-title">Client & Category</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="client_id">Client Company *</label>
                    <select id="client_id" name="client_id" class="form-control" required>
                        <option value="">Select a client...</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?php echo $c['client_id']; ?>"
                                <?php echo ($f('client_id') == $c['client_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['company_name']); ?>
                                (<?php echo $c['client_type'] === 'linked' ? '🔗' : '📝'; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo ($f('category_id') == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <hr class="form-divider">
            <div class="form-section-title">Job Details</div>

            <div class="form-group">
                <label for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-control"
                       placeholder="e.g. Senior Laravel Developer"
                       value="<?php echo htmlspecialchars($f('title')); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Job Description *</label>
                <textarea id="description" name="description" class="form-control" rows="4"
                          placeholder="Describe the role, responsibilities, and what the candidate will work on..."
                          required><?php echo htmlspecialchars($f('description')); ?></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="requirements">Requirements</label>
                    <textarea id="requirements" name="requirements" class="form-control" rows="3"
                              placeholder="e.g. PHP, Laravel, MySQL, 3+ years..."><?php echo htmlspecialchars($f('requirements')); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="benefits">Benefits</label>
                    <textarea id="benefits" name="benefits" class="form-control" rows="3"
                              placeholder="e.g. Health insurance, remote work..."><?php echo htmlspecialchars($f('benefits')); ?></textarea>
                </div>
            </div>

            <hr class="form-divider">
            <div class="form-section-title">Compensation & Location</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="salary_min">Salary Min (৳)</label>
                    <input type="number" id="salary_min" name="salary_min" class="form-control"
                           placeholder="e.g. 40000" min="0" value="<?php echo htmlspecialchars($f('salary_min')); ?>">
                </div>
                <div class="form-group">
                    <label for="salary_max">Salary Max (৳)</label>
                    <input type="number" id="salary_max" name="salary_max" class="form-control"
                           placeholder="e.g. 80000" min="0" value="<?php echo htmlspecialchars($f('salary_max')); ?>">
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control"
                           placeholder="e.g. Dhaka, Remote"
                           value="<?php echo htmlspecialchars($f('location')); ?>">
                </div>
            </div>

            <hr class="form-divider">
            <div class="form-section-title">Type & Status</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="job_type">Job Type *</label>
                    <select id="job_type" name="job_type" class="form-control" required>
                        <?php foreach ($jobTypes as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo ($f('job_type') === $k) ? 'selected' : ''; ?>>
                                <?php echo $v; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="experience_level">Experience Level *</label>
                    <select id="experience_level" name="experience_level" class="form-control" required>
                        <?php foreach ($expLevels as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo ($f('experience_level') === $k) ? 'selected' : ''; ?>>
                                <?php echo $v; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="deadline">Application Deadline</label>
                    <input type="date" id="deadline" name="deadline" class="form-control"
                           value="<?php echo htmlspecialchars($f('deadline')); ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <?php foreach ($statuses as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo ($f('status') === $k) ? 'selected' : ''; ?>>
                                <?php echo $v; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-submit-row">
                <a href="jobs.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save"><?php echo $isEdit ? 'Update Job' : 'Post Job'; ?></button>
            </div>
        </form>
    </div>

    <?php endif; ?>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const minInput = document.getElementById('salary_min');
    const maxInput = document.getElementById('salary_max');

    function validateSalaries() {
        const min = parseFloat(minInput.value) || 0;
        const max = parseFloat(maxInput.value) || 0;

        if (max > 0 && min > max) {
            maxInput.setCustomValidity('Maximum salary cannot be lower than minimum salary.');
        } else {
            maxInput.setCustomValidity('');
        }
    }

    minInput.addEventListener('input', validateSalaries);
    maxInput.addEventListener('input', validateSalaries);
});
</script>

</body>
</html>


