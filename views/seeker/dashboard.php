<?php
// views/seeker/dashboard.php — No logic. Uses $stats, $applications, $matchedJobs from controller.
define('BASE_URL', '../');
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>

    <!-- Stats -->
    <div class="metric-grid">
        <div class="metric-card"><span class="metric-val"><?= $stats['total'] ?></span><span class="metric-label">Applied</span></div>
        <div class="metric-card"><span class="metric-val"><?= $stats['shortlisted'] ?></span><span class="metric-label">Shortlisted</span></div>
        <div class="metric-card"><span class="metric-val"><?= $stats['interview'] ?></span><span class="metric-label">Interviews</span></div>
        <div class="metric-card"><span class="metric-val"><?= $stats['saved'] ?></span><span class="metric-label">Saved Jobs</span></div>
    </div>

    <div class="two-col">
        <!-- Recent Applications -->
        <section class="card">
            <h2>Recent Applications</h2>
            <?php if (empty($applications)): ?>
                <p class="muted">No applications yet. <a href="index.php?action=jobs">Find jobs →</a></p>
            <?php else: ?>
                <ul class="app-list">
                    <?php foreach ($applications as $app): ?>
                    <li>
                        <strong><?= htmlspecialchars($app['job_title']) ?></strong>
                        <span><?= htmlspecialchars($app['company_name'] ?? '') ?></span>
                        <span class="status-badge status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="index.php?action=applications">View all →</a>
            <?php endif; ?>
        </section>

        <!-- Matched Alerts -->
        <section class="card">
            <h2>Matched Job Alerts</h2>
            <?php if (empty($matchedJobs)): ?>
                <p class="muted">No matches yet. <a href="index.php?action=alerts">Set up alerts →</a></p>
            <?php else: ?>
                <ul class="app-list">
                    <?php foreach ($matchedJobs as $job): ?>
                    <li>
                        <a href="index.php?action=jobDetail&id=<?= (int)$job['id'] ?>">
                            <?= htmlspecialchars($job['title']) ?>
                        </a>
                        <span><?= htmlspecialchars($job['company_name'] ?? '') ?> · <?= htmlspecialchars($job['location']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>