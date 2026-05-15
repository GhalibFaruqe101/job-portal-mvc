<?php
session_start();
// Grab any flash messages
$errors = $_SESSION['auth_errors'] ?? [];
$old_name = $_SESSION['old_name'] ?? '';
$old_email = $_SESSION['old_email'] ?? '';
$old_phone = $_SESSION['old_phone'] ?? '';
unset($_SESSION['auth_errors'], $_SESSION['old_name'], $_SESSION['old_email'], $_SESSION['old_phone']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Seeker Account - JobPortal</title>
    <meta name="description" content="Register as a Job Seeker on JobPortal to start searching and applying for jobs.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/seeker/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="role-badge">
            <span class="badge-icon">🔍</span>
            Job Seeker
        </div>

        <h1>Create Account</h1>
        <p class="subtitle">Start your job search journey today</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" action="../controllers/AuthController.php?action=register" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="John Doe"
                       value="<?php echo htmlspecialchars($old_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($old_email); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       placeholder="01XXXXXXXXX"
                       value="<?php echo htmlspecialchars($old_phone); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="At least 6 characters" required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                       placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn-auth">Create Account</button>
        </form>

        <div class="auth-divider"><span>or</span></div>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign In</a>
        </div>

        <div style="text-align:center;">
            <a href="../../index.php" class="back-link">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
