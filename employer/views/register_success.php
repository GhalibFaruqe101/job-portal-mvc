<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>

<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="AuthController.php?action=login">Login</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%; text-align: center;">
        <div class="card" style="max-width: 600px; margin: 0 auto; padding: 3rem;">
            <h1 style="color: #28a745;">Registration Successful!</h1>
            <p style="font-size: 1.1rem; margin-top: 1rem;">
                <?php echo htmlspecialchars($success ?? 'Your account has been created.'); ?>
            </p>
            <p style="margin-top: 1rem; color: #666;">
                We will review your account details and approve shortly.
            </p>
            <div style="margin-top: 2rem;">
                <a href="login.php" class="btn-primary" style="text-decoration: none;">Go to
                    Login</a>
            </div>
        </div>
    </main>
</body>

</html>