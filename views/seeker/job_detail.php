<?php
// views/seeker/job_detail.php
define('BASE_URL', '../');
$pageTitle = htmlspecialchars($job['title']);
$activeNav = 'jobs';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <div class="job-detail-header">
        <?php if (!empty($job['logo_path'])): ?>
            <img src="<?= htmlspecialchars($job['logo_path']) ?>" class="company-logo-lg" alt="">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($job['title']) ?></h1>
            <p class="job-company">
                <?= htmlspecialchars($job['company_name'] ?? 'Unknown') ?>
                <?php if (!empty($job['agency_name'])): ?>
                    <em>via <?= htmlspecialchars($job['agency_name']) ?></em>
                <?php endif; ?>
            </p>
            <div class="badge-row">
                <span class="badge"><?= htmlspecialchars($job['job_type']) ?></span>
                <span class="badge"><?= htmlspecialchars($job['experience_level']) ?></span>
                <span class="badge">📍 <?= htmlspecialchars($job['location']) ?></span>
                <?php if ($job['salary_min']): ?>
                    <span class="badge salary">৳ <?= number_format($job['salary_min']) ?>–<?= number_format($job['salary_max']) ?></span>
                <?php endif; ?>
                <?php if ($job['is_featured']): ?><span class="badge featured">Featured</span><?php endif; ?>
            </div>
            <p class="muted">Deadline: <?= $job['deadline'] ? date('d M Y', strtotime($job['deadline'])) : 'Open' ?></p>
        </div>

        <!-- Save/Unsave (AJAX) -->
        <form method="post" action="index.php?action=saveJob" id="save-form">
            <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
            <button type="button" id="save-btn" class="btn-outline" onclick="toggleSave(<?= (int)$job['id'] ?>)">
                <?= $isSaved ? '🔖 Saved' : '+ Save Job' ?>
            </button>
        </form>
    </div>

    <div class="two-col">
        <div>
            <section class="card">
                <h2>Job Description</h2>
                <div class="prose"><?= nl2br(htmlspecialchars($job['description'])) ?></div>
            </section>
            <?php if ($job['requirements']): ?>
            <section class="card">
                <h2>Requirements</h2>
                <div class="prose"><?= nl2br(htmlspecialchars($job['requirements'])) ?></div>
            </section>
            <?php endif; ?>
            <?php if ($job['benefits']): ?>
            <section class="card">
                <h2>Benefits</h2>
                <div class="prose"><?= nl2br(htmlspecialchars($job['benefits'])) ?></div>
            </section>
            <?php endif; ?>
        </div>

        <div>
            <!-- Company Info -->
            <section class="card">
                <h2>About the Company</h2>
                <p><strong><?= htmlspecialchars($job['company_name'] ?? '') ?></strong></p>
                <?php if (!empty($job['industry'])): ?><p><?= htmlspecialchars($job['industry']) ?></p><?php endif; ?>
                <?php if (!empty($job['company_desc'])): ?><p><?= nl2br(htmlspecialchars($job['company_desc'])) ?></p><?php endif; ?>
                <?php if (!empty($job['website'])): ?><p><a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" rel="noopener">Company Website ↗</a></p><?php endif; ?>
                <p class="muted">Posted by: <?= htmlspecialchars($job['poster_name'] ?? '—') ?></p>
                <a href="index.php?action=complaint&subject_id=<?= (int)$job['employer_id'] ?>" class="muted small">
                    Report this posting
                </a>
            </section>

            <!-- Apply Form -->
            <?php if ($alreadyApplied): ?>
                <div class="card notice">You have already applied to this job.</div>
            <?php else: ?>
            <section class="card">
                <h2>Apply Now</h2>
                <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <form method="post" action="index.php?action=applyJob" enctype="multipart/form-data">
                    <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
                    <label>Cover Letter
                        <textarea name="cover_letter" rows="5" placeholder="Introduce yourself and explain why you're a great fit…"></textarea>
                    </label>
                    <label>Resume
                        <?php if (!empty($profile['resume_path'])): ?>
                            <p class="muted">Using your profile resume. Or upload a new one:</p>
                        <?php endif; ?>
                        <input type="file" name="resume" accept="application/pdf">
                        <small>PDF only, max 5 MB. Leave blank to use profile resume.</small>
                    </label>
                    <button type="submit" class="btn">Submit Application</button>
                </form>
            </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleSave(jobId) {
    const btn = document.getElementById('save-btn');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?action=saveJob', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const res = JSON.parse(xhr.responseText);
            btn.textContent = res.saved ? '🔖 Saved' : '+ Save Job';
        }
    };
    xhr.send('job_id=' + jobId);
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>