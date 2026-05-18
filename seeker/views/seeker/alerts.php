<?php

$pageTitle = 'Job Alerts';
$activeNav = 'alerts';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Job Alerts</h1>

  
    <section class="card">
        <h2>Create New Alert</h2>
        <form method="post" action="<?= BASE_PATH ?>/index.php?action=createAlert" class="inline-form">
            <input type="text" name="keyword" placeholder="Keyword (e.g. software engineer)" required>
            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="location" placeholder="Location (optional)">
            <select name="job_type">
                <option value="">Any type</option>
                <?php foreach (['full-time','part-time','remote','contract'] as $t): ?>
                    <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">+ Add Alert</button>
        </form>
    </section>

    <!-- Active alerts -->
    <section class="card">
        <h2>Active Alerts</h2>
        <?php if (empty($alerts)): ?>
            <p class="muted">No alerts set up yet.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Keyword</th><th>Category</th><th>Location</th><th>Job Type</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($alerts as $alert): ?>
            <tr>
                <td><?= htmlspecialchars($alert['keyword']) ?></td>
                <td><?= htmlspecialchars($alert['category_name'] ?? 'All') ?></td>
                <td><?= htmlspecialchars($alert['location'] ?: 'Any') ?></td>
                <td><?= htmlspecialchars($alert['job_type'] ?: 'Any') ?></td>
                <td>
                    <form method="post" action="<?= BASE_PATH ?>/index.php?action=deleteAlert"
                          onsubmit="return confirm('Delete this alert?')">
                        <input type="hidden" name="alert_id" value="<?= (int)$alert['id'] ?>">
                        <button type="submit" class="btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>