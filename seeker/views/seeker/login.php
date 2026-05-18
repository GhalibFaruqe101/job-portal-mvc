<?php

define('BASE_URL', '../');
$pageTitle = 'Login';
require __DIR__ . '/../layouts/header.php';
?>
<div class="auth-container">
    <h1>Seeker Login</h1>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" action="index.php?action=doLogin">
        <label>Email<input type="email" name="email" required autofocus></label>
        <label>Password<input type="password" name="password" required></label>
        <button type="submit" class="btn btn-full">Login</button>
    </form>
    <p>Don't have an account? <a href="index.php?action=register">Register</a></p>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>