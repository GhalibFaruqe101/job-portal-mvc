<?php
require_once '../helpers/session.php';
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
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* Enhanced success banner */
        .alert-success-banner {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.4);
            border-left: 4px solid #10b981;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            color: #6ee7b7;
            font-size: 0.9rem;
            animation: bannerIn 0.4s ease, bannerFade 0.6s ease 4.4s forwards;
        }
        .alert-success-banner .banner-icon {
            font-size: 1.4rem;
            line-height: 1;
            flex-shrink: 0;
        }
        .alert-success-banner .banner-text strong {
            display: block;
            color: #34d399;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        @keyframes bannerIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes bannerFade {
            from { opacity: 1; }
            to   { opacity: 0; pointer-events: none; }
        }
    </style>
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
            <div class="alert-success-banner" id="successBanner">
                <div class="banner-icon">✅</div>
                <div class="banner-text">
                    <strong>Registration Successful!</strong>
                    <?php echo htmlspecialchars($success); ?> You can now sign in below.
                </div>
            </div>
            <script>
                // Auto-remove after animation completes (5s)
                setTimeout(function() {
                    var b = document.getElementById('successBanner');
                    if (b) b.remove();
                }, 5000);
            </script>
        <?php endif; ?>

        <form class="auth-form" action="../controllers/AuthController.php?action=login" method="POST">
            <?php echo csrfInput(); ?>
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
