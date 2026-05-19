<?php
// views/seeker/complaint.php
$pageTitle = 'Submit Complaint';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Report a Problem</h1>
    <p class="muted">Submit a complaint about a misleading job posting or employer conduct. Admin will review it.</p>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" action="index.php?action=submitComplaint" class="profile-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
        <input type="hidden" name="subject_id" value="<?= (int)$subjectId ?>">
        <label>Description *
            <textarea name="description" rows="5" required
                      placeholder="Describe the issue in detail…"></textarea>
        </label>
        <div class="form-actions">
            <button type="submit" class="btn">Submit Complaint</button>
            <a href="javascript:history.back()" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>