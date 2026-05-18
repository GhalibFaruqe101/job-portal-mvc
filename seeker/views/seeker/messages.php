<?php

$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Messages</h1>
    <?php if (empty($messages)): ?>
        <p class="muted">No messages yet.</p>
    <?php else: ?>
    <div class="message-list">
        <?php foreach ($messages as $msg): ?>
        <div class="message-item <?= $msg['is_read'] ? '' : 'unread' ?>">
            <div class="msg-meta">
                <strong><?= htmlspecialchars($msg['sender_name']) ?></strong>
                <?php if (!empty($msg['job_title'])): ?>
                    <span class="muted">re: <?= htmlspecialchars($msg['job_title']) ?></span>
                <?php endif; ?>
                <span class="muted"><?= date('d M Y, g:i a', strtotime($msg['sent_at'])) ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($msg['body'])) ?></p>
            <!-- Reply -->
            <details class="reply-toggle">
                <summary class="btn-sm">Reply</summary>
                <form method="post" action="<?= BASE_PATH ?>/index.php?action=sendMessage" class="reply-form">
                    <input type="hidden" name="recipient_id" value="<?= (int)$msg['sender_id'] ?>">
                    <input type="hidden" name="application_id" value="<?= (int)($msg['application_id'] ?? 0) ?>">
                    <textarea name="body" rows="3" placeholder="Write your reply…" required></textarea>
                    <button type="submit" class="btn">Send</button>
                </form>
            </details>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>