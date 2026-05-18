<?php
// views/seeker/saved_jobs.php
$pageTitle = 'Saved Jobs';
$activeNav = 'saved';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Saved Jobs</h1>
    <?php if (empty($jobs)): ?>
        <p class="muted">No saved jobs. <a href="/job_portal/seeker/index.php?action=jobs">Browse jobs →</a></p>
    <?php else: ?>
    <div class="job-list">
        <?php foreach ($jobs as $job): ?>
        <div class="job-card">
            <div class="job-card-top">
                <div>
                    <a href="/job_portal/seeker/index.php?action=jobDetail&id=<?= (int)$job['job_id'] ?>" class="job-title">
                        <?= htmlspecialchars($job['title']) ?>
                    </a>
                    <p class="job-company"><?= htmlspecialchars($job['company_name'] ?? '') ?></p>
                    <div class="badge-row">
                        <span class="badge"><?= htmlspecialchars($job['job_type']) ?></span>
                        <span class="badge">📍 <?= htmlspecialchars($job['location']) ?></span>
                        <?php if ($job['deadline'] && strtotime($job['deadline']) < strtotime('+3 days')): ?>
                            <span class="badge badge-warn">Deadline soon</span>
                        <?php endif; ?>
                        <?php if ($job['job_status'] !== 'active'): ?>
                            <span class="badge badge-gray">Closed</span>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Unsave -->
                <form method="post" action="/job_portal/seeker/index.php?action=saveJob">
                    <input type="hidden" name="job_id" value="<?= (int)$job['job_id'] ?>">
                    <button type="submit" class="btn-sm btn-danger">Remove</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>