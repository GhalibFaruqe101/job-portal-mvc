<?php
// views/seeker/register.php
define('BASE_URL', '../');
$pageTitle = 'Register';
require __DIR__ . '/../layouts/header.php';
?>
<div class="auth-container">
    <h1>Create Seeker Account</h1>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" action="index.php?action=doRegister">
        <label>Full Name<input type="text" name="name" required autofocus></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Phone<input type="tel" name="phone" required></label>
        <label>Password<input type="password" name="password" required minlength="6"></label>
        <label>Confirm Password<input type="password" name="confirm_pass" required></label>
        <button type="submit" class="btn btn-full">Register</button>
    </form>
    <p>Already registered? <a href="index.php?action=login">Login</a></p>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>