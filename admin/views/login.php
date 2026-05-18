<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/view.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Job Portal</title>
    <link rel="stylesheet" href="<?= e($assetBase ?? '../') ?>public/css/style.css">
    <link rel="stylesheet" href="<?= e($assetBase ?? '../') ?>public/css/admin/admin.css">
</head>
<body class="admin-login-body">
    <main class="admin-login-wrap">
        <section class="card admin-login-card">
            <h1>Admin Login</h1>
            <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
            <form method="post" action="index.php?action=authenticate" class="admin-form">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Password">
                </div>
                <button type="submit" class="btn-primary admin-btn-full">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
