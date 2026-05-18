<?php

$pageTitle = 'Edit Profile';
$activeNav = 'profile';
require __DIR__ . '/../layouts/header.php';
?>
<div class="container">
    <h1>Edit Profile</h1>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" action="/job_portal/seeker/index.php?action=saveProfile" class="profile-form">
        <label>Headline *
            <input type="text" name="headline" required
                   value="<?= htmlspecialchars($profile['headline'] ?? '') ?>"
                   placeholder="e.g. Full Stack Developer · PHP & React">
        </label>
        <label>Professional Summary
            <textarea name="summary" rows="4"><?= htmlspecialchars($profile['summary'] ?? '') ?></textarea>
        </label>
        <label>Skills (comma-separated)
            <input type="text" name="skills"
                   value="<?= htmlspecialchars($profile['skills'] ?? '') ?>"
                   placeholder="PHP, MySQL, React, Laravel">
        </label>
        <label>Years of Experience
            <input type="number" name="years_experience" min="0" max="50"
                   value="<?= (int)($profile['years_experience'] ?? 0) ?>">
        </label>
        <label>Education Level
            <select name="education_level">
                <?php foreach (['SSC','HSC','Diploma','Bachelor','Master','PhD','Other'] as $lv): ?>
                    <option value="<?= $lv ?>" <?= ($profile['education_level']??'') === $lv ? 'selected':'' ?>><?= $lv ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Current Monthly Salary 
            <input type="number" name="current_salary" min="0" step="500"
                   value="<?= (float)($profile['current_salary'] ?? 0) ?>">
        </label>
        <label>Expected Monthly Salary 
            <input type="number" name="expected_salary" min="0" step="500"
                   value="<?= (float)($profile['expected_salary'] ?? 0) ?>">
        </label>
        <label>Preferred Location
            <input type="text" name="preferred_location"
                   value="<?= htmlspecialchars($profile['preferred_location'] ?? '') ?>"
                   placeholder="Dhaka / Remote">
        </label>
        <div class="form-actions">
            <button type="submit" class="btn">Save Profile</button>
            <a href="/job_portal/seeker/index.php?action=profile" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>