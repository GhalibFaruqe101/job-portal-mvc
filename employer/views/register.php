<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Registration - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>

<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="../controllers/AuthController.php?action=login">Login</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h1>Employer Registration</h1>
            <p>Register your company to post jobs and find talent.</p>

            <?php if (!empty($error))
                echo "<p style='color:red; font-weight:bold; margin-bottom:1rem;'>$error</p>"; ?>

            <form method="POST" action="../controllers/RegisterController.php?action=users" enctype="multipart/form-data">
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Name (Company/Contact) *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter company name" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter company email address" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Password *</label>
                    <input type="password" name="password_hash" class="form-control" placeholder="Create a secure password" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                </div>

                <input type="hidden" name="role" value="employer">

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">Register Company</button>
                </div>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="../controllers/AuthController.php?action=login">Login here</a>.
            </p>
        </div>
    </main>
</body>

</html>