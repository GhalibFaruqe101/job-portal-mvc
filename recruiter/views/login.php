<?php
session_start();
$errors    = $_SESSION['auth_errors'] ?? [];
$success   = $_SESSION['auth_success'] ?? '';
$old_email = $_SESSION['old_email'] ?? '';
unset($_SESSION['auth_errors'], $_SESSION['auth_success'], $_SESSION['old_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Login - JobPortal</title>
    <meta name="description" content="Log in to your Recruiter account on JobPortal to manage candidates and job placements.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="role-badge">
            <span class="badge-icon">🎯</span>
            Recruiter
        </div>

        <h1>Welcome Back</h1>
        <p class="subtitle">Sign in to manage your talent pipeline</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" action="../controllers/AuthController.php?action=login" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@agency.com"
                       value="<?php echo htmlspecialchars($old_email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="auth-divider"><span>or</span></div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>

        <div style="text-align:center;">
            <a href="../../index.php" class="back-link">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
