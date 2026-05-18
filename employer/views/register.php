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
            <a href="AuthController.php?action=login">Login</a>
            <a href="RegisterController.php?action=register">Register</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h1>Employer Registration</h1>
            <p>Register your company to post jobs and find talent.</p>

            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

            <form method="POST" action="?action=register" enctype="multipart/form-data">
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Company Name *</label>
                    <input type="text" name="company" class="form-control" placeholder="Enter company name" required>
                </div>
                
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Industry</label>
                    <input type="text" name="industry" class="form-control" placeholder="e.g., Technology, Healthcare">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Company Size</label>
                    <select name="size" class="form-control">
                        <option value="">Select size</option>
                        <option value="1-10">1-10 employees</option>
                        <option value="11-50">11-50 employees</option>
                        <option value="51-200">51-200 employees</option>
                        <option value="201-500">201-500 employees</option>
                        <option value="500+">500+ employees</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Briefly describe your company"></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Website</label>
                    <input type="url" name="website" class="form-control" placeholder="https://example.com">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Company address">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">Register Company</button>
                </div>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="AuthController.php?action=login">Login here</a>.
            </p>
        </div>
    </main>
</body>
</html>
