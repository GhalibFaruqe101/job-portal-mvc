<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Login - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="login.php">Login</a>
        </div>
    </nav>
    <main style="padding: 2rem 5%;">
        <div class="card">
            <h1>Employer Login</h1>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" action="?action=login">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </main>
</body>
</html>
