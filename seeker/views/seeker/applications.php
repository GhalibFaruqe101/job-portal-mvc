<?php
// views/seeker/applications.php
$pageTitle = 'My Applications';
$activeNav = 'applications';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>My Applications</h1>
    <?php if (empty($applications)): ?>
        <p class="muted">You haven't applied to any jobs yet. <a href="index.php?action=jobs">Browse jobs →</a></p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Job</th><th>Company</th><th>Location</th><th>Applied</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($applications as $app): ?>
            <tr>
                <td><a href="index.php?action=jobDetail&id=<?= (int)$app['job_id'] ?>"><?= htmlspecialchars($app['job_title']) ?></a></td>
                <td><?= htmlspecialchars($app['company_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($app['location'] ?? '—') ?></td>
                <td><?= date('d M Y', strtotime($app['applied_at'])) ?></td>
                <td><span class="status-badge status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span></td>
                <td>
                    <?php if ($app['status'] === 'submitted'): ?>
                        <form method="post" action="index.php?action=withdraw"
                              onsubmit="return confirm('Withdraw this application?')">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                            <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
                            <button type="submit" class="btn-sm btn-danger">Withdraw</button>
                        </form>
                    <?php else: ?>
                        <span class="muted">—</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>