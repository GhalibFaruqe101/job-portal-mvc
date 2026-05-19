<?php
$pageTitle = 'Recruiter Outreach';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <h2>Recruiter Outreach</h2>
    <p style="color: #666; margin-bottom: 30px;">Recruiters who contacted you about specific opportunities:</p>

    <?php if (empty($outreach_messages)): ?>
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; text-align: center; color: #777;">
            No outreach messages yet.
        </div>
    <?php else: ?>
        <div class="outreach-list">
            <?php foreach ($outreach_messages as $msg): ?>
                <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; color: #2c3e50;">Role: <?= htmlspecialchars($msg['job_title'] ?? 'Unknown Job') ?></h3>
                    
                    <div style="margin-bottom: 15px; font-size: 0.95em; color: #555;">
                        <strong>From:</strong> <?= htmlspecialchars($msg['recruiter_name'] ?? 'Unknown') ?> 
                        <span style="color: #888;">(<?= htmlspecialchars($msg['agency_name'] ?? 'Direct Employer') ?>)</span>
                        <br>
                        <strong>Date:</strong> <?= date('F j, Y, g:i a', strtotime($msg['sent_at'])) ?>
                    </div>

                    <div style="background: #f4f6f8; padding: 15px; border-left: 4px solid #3498db; border-radius: 4px; color: #444; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>