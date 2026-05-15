<?php
session_start();
$errors     = $_SESSION['auth_errors'] ?? [];
$old_name   = $_SESSION['old_name'] ?? '';
$old_email  = $_SESSION['old_email'] ?? '';
$old_phone  = $_SESSION['old_phone'] ?? '';
$old_agency = $_SESSION['old_agency'] ?? '';
unset($_SESSION['auth_errors'], $_SESSION['old_name'], $_SESSION['old_email'],
      $_SESSION['old_phone'], $_SESSION['old_agency']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Recruiter Account - JobPortal</title>
    <meta name="description" content="Register as a Recruiter on JobPortal to start sourcing and placing top talent.">
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

        <h1>Create Account</h1>
        <p class="subtitle">Start placing top talent with JobPortal</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
            </div>
        <?php endif; ?>

        <form class="auth-form" action="../controllers/AuthController.php?action=register" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="Jane Smith"
                       value="<?php echo htmlspecialchars($old_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="agency_name">Agency / Company Name</label>
                <input type="text" id="agency_name" name="agency_name" class="form-control"
                       placeholder="TalentBridge Agency"
                       value="<?php echo htmlspecialchars($old_agency); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@agency.com"
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
