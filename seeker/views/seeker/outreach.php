<?php
// views/seeker/outreach.php
define('BASE_URL', '../');
$pageTitle = 'Recruiter Outreach';
$activeNav = 'messages';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Recruiter Outreach</h1>
    <p class="muted">Recruiters who contacted you about specific opportunities:</p>
    <?php if (empty($outreach)): ?>
        <p class="muted">No outreach messages yet.</p>
    <?php else: ?>
    <div class="message-list">
        <?php foreach ($outreach as $o): ?>
        <div class="message-item <?= $o['status'] === 'sent' ? 'unread' : '' ?>">
            <div class="msg-meta">
                <strong><?= htmlspecialchars($o['recruiter_name']) ?></strong>
                <?php if (!empty($o['agency_name'])): ?>
                    <span class="muted">(<?= htmlspecialchars($o['agency_name']) ?>)</span>
                <?php endif; ?>
                <?php if (!empty($o['job_title'])): ?>
                    <span class="muted">re: <?= htmlspecialchars($o['job_title']) ?></span>
                <?php endif; ?>
                <span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($o['message'])) ?></p>
            <?php if ($o['status'] === 'sent'): ?>
            <form method="post" action="index.php?action=respondOutreach" style="display:inline-flex;gap:8px">
                <input type="hidden" name="outreach_id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="status" value="responded">
                <button type="submit" class="btn-sm">Mark as Responded</button>
            </form>
            <?php endif; ?>
            <?php if (!empty($o['job_id'])): ?>
                <a href="index.php?action=jobDetail&id=<?= (int)$o['job_id'] ?>" class="btn-sm">View Job</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>