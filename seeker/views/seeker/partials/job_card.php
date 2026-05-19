<?php

?>
<div class="job-card <?= $job['is_featured'] ? 'featured' : '' ?>">
    <div class="job-card-top">
        <?php if (!empty($job['logo_path'])): ?>
            <img src="<?= htmlspecialchars($job['logo_path']) ?>" class="company-logo" alt="">
        <?php endif; ?>

        <div style="flex:1">
            <a href="index.php?action=jobDetail&id=<?= (int)$job['id'] ?>" class="job-title">
                <?= htmlspecialchars($job['title']) ?>
            </a>
            <p class="job-company">
                <?= htmlspecialchars($job['company_name'] ?? 'Unknown') ?>
                <?php if (!empty($job['agency_name'])): ?>
                    <em>via <?= htmlspecialchars($job['agency_name']) ?></em>
                <?php endif; ?>
            </p>
            <div class="badge-row">
                <span class="badge"><?= htmlspecialchars($job['job_type']) ?></span>
                <span class="badge"><?= htmlspecialchars($job['experience_level']) ?></span>
                <?php if ($job['salary_min']): ?>
                    <span class="badge salary">
                        ৳ <?= number_format($job['salary_min']) ?>–<?= number_format($job['salary_max']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($job['is_featured']): ?>
                    <span class="badge featured">Featured</span>
                <?php endif; ?>
                <?php if (!empty($job['category_name'])): ?>
                    <span class="badge"><?= htmlspecialchars($job['category_name']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <form method="post" action="index.php?action=saveJob" style="flex-shrink:0">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
            <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
            <button type="submit" class="btn-sm" title="Save job">🔖 Save</button>
        </form>
    </div>

    <div class="job-card-footer">
        <span class="muted"> <?= htmlspecialchars($job['location']) ?></span>
        <span class="muted">
            Deadline: <?= !empty($job['deadline']) ? date('d M Y', strtotime($job['deadline'])) : 'Open' ?>
        </span>
        <a href="index.php?action=jobDetail&id=<?= (int)$job['id'] ?>" class="btn-sm">
            View &amp; Apply
        </a>
    </div>
</div>
