<?php
define('BASE_URL', '../');
$pageTitle = 'My Profile';
$activeNav = 'profile';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="profile-header-card">
        <div class="avatar-wrap">
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="/job_portal/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile picture" class="profile-pic">
            <?php else: ?>
                <div class="avatar-initials"><?= strtoupper(substr($user['name'], 0, 2)) ?></div>
            <?php endif; ?>
            <form method="post" action="index.php?action=uploadPic" enctype="multipart/form-data" class="inline-form">
                <label class="btn-sm" for="pic-input">Change photo</label>
                <input id="pic-input" type="file" name="profile_pic" accept="image/jpeg,image/png,image/webp"
                       onchange="this.form.submit()" style="display:none">
            </form>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['name']) ?></h1>
            <p class="headline"><?= htmlspecialchars($profile['headline'] ?? 'No headline set') ?></p>
            <p class="meta"><?= htmlspecialchars($user['email']) ?> · <?= htmlspecialchars($user['phone']) ?></p>
            <a href="index.php?action=editProfile" class="btn">Edit Profile</a>
        </div>
    </div>

    <div class="profile-grid">
        <section class="card">
            <h2>Summary</h2>
            <p><?= !empty($profile['summary']) ? nl2br(htmlspecialchars($profile['summary'])) : 'No summary added yet.' ?></p>
        </section>

        <section class="card">
            <h2>Skills</h2>
            <div class="skill-tags">
                <?php
                $skills = array_filter(array_map('trim', explode(',', $profile['skills'] ?? '')));
                foreach ($skills as $skill): ?>
                    <span class="tag"><?= htmlspecialchars($skill) ?></span>
                <?php endforeach; ?>
                <?php if (empty($skills)): ?><p class="muted">No skills added.</p><?php endif; ?>
            </div>
        </section>

        <section class="card">
            <h2>Experience & Education</h2>
            <table class="info-table">
                <tr><th>Years of experience</th><td><?= isset($profile['years_experience']) ? (int)$profile['years_experience'] : 0 ?></td></tr>
                <tr><th>Education level</th>    <td><?= !empty($profile['education_level']) ? htmlspecialchars($profile['education_level']) : '—' ?></td></tr>
                <tr><th>Current salary</th>      <td>৳ <?= isset($profile['current_salary']) ? number_format((float)$profile['current_salary']) : 0 ?>/mo</td></tr>
                <tr><th>Expected salary</th>     <td>৳ <?= isset($profile['expected_salary']) ? number_format((float)$profile['expected_salary']) : 0 ?>/mo</td></tr>
                <tr><th>Preferred location</th>  <td><?= !empty($profile['preferred_location']) ? htmlspecialchars($profile['preferred_location']) : '—' ?></td></tr>
            </table>
        </section>

        <section class="card">
            <h2>Resume</h2>
            <?php if (!empty($profile['resume_path'])): ?>
                <p>
                    <a href="/job_portal/<?= htmlspecialchars($profile['resume_path']) ?>" download class="btn-sm">
                        ⬇ Download Resume
                    </a>
                    <span class="muted">(PDF Connected)</span>
                </p>
            <?php else: ?>
                <p class="muted">No resume uploaded yet.</p>
            <?php endif; ?>
            <form method="post" action="index.php?action=uploadResume" enctype="multipart/form-data" class="upload-form">
                <label>Upload / replace resume (PDF, max 5 MB):</label>
                <input type="file" name="resume" accept="application/pdf" required>
                <button type="submit" class="btn">Upload</button>
            </form>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>