<?php
$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/../layouts/header.php';

// লগইন করা ইউজারের আইডি নেওয়া হলো
$my_id = $_SESSION['user_id'];
?>
<div class="container">
    <h1>Messages</h1>
    
    <?php if (empty($messages)): ?>
        <p class="muted">No messages yet.</p>
    <?php else: ?>
    <div class="message-list">
        <?php foreach ($messages as $msg): ?>
        
        <?php 
            // চেক করা হচ্ছে মেসেজটি আপনি পাঠিয়েছেন নাকি রিসিভ করেছেন
            $is_sent_by_me = ($msg['sender_id'] == $my_id);
        ?>

        <div class="message-item <?= $msg['is_read'] ? '' : 'unread' ?>" style="<?= $is_sent_by_me ? 'margin-left: 40px; border-left: 4px solid #4CAF50; background: #f9fff9;' : 'margin-right: 40px; border-left: 4px solid #2196F3;' ?>">
            <div class="msg-meta">
                <?php if ($is_sent_by_me): ?>
                    <strong>You</strong> <span class="muted">replied to</span> <strong><?= htmlspecialchars($msg['recipient_name'] ?? 'Recruiter') ?></strong>
                <?php else: ?>
                    <strong><?= htmlspecialchars($msg['sender_name']) ?></strong>
                <?php endif; ?>
                
                <?php if (!empty($msg['job_title'])): ?>
                    <span class="muted">re: <?= htmlspecialchars($msg['job_title']) ?></span>
                <?php endif; ?>
                
                <span class="muted" style="float: right;"><?= date('d M Y, g:i a', strtotime($msg['sent_at'])) ?></span>
            </div>
            
            <p style="margin-top: 10px;"><?= nl2br(htmlspecialchars($msg['body'])) ?></p>
            
            <?php if (!$is_sent_by_me): ?>
            <details class="reply-toggle">
                <summary class="btn-sm">Reply</summary>
                <form method="post" action="index.php?action=sendMessage" class="reply-form" style="margin-top: 10px;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <input type="hidden" name="recipient_id" value="<?= (int)$msg['sender_id'] ?>">
                    <input type="hidden" name="application_id" value="<?= (int)($msg['application_id'] ?? 0) ?>">
                    <textarea name="body" rows="3" placeholder="Write your reply…" required style="width: 100%; margin-bottom: 10px;"></textarea>
                    <button type="submit" class="btn">Send</button>
                </form>
            </details>
            <?php endif; ?>
            
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>