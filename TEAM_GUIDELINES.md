Job Portal - Team Integration & Coding Guidelines
=============================================

1. Directory Architecture (The Sandbox)
--------------------------------------
To prevent Git merge conflicts, each member must work only inside their assigned folder. Do not edit files outside your module unless it's a shared CSS/JS file in the `/public` folder.

Project layout (shared assets + per-role workspaces):

```
/Project
├── /public                      # SHARED ASSETS
│   ├── /css
│   │   ├── style.css            # Global styles (Navbar, Footer, Typography)
│   │   ├── /seeker              # Jannatul's CSS
│   │   ├── /employer            # Member 2's CSS
│   │   ├── /recruiter           # Member 3's CSS
│   │   └── /admin               # Member 4's CSS
│   └── /js                      # (Same structure as CSS)
│
├── /seeker                      # Member 1's Workspace
│   ├── /config                  # Put your db.php here
│   ├── /helpers                 # Put your session.php here
│   ├── /controllers 
│   ├── /models                  
│   └── /views                   
│
├── /employer                    # Member 2's Workspace
├── /recruiter                   # Member 3's Workspace
└── /admin                       # Member 4's Workspace

```

2. The Data Contract: Sessions (MANDATORY)
-----------------------------------------
Each module will implement its own `login.php` / `AuthController.php`. On successful login you MUST set the following exact session keys (case-sensitive):

- `$_SESSION['user_id']` : The integer ID from the `users` table.
- `$_SESSION['role']` : The exact role string, one of `'seeker'`, `'employer'`, `'recruiter'`, or `'admin'`.

Do NOT use alternate names such as `$_SESSION['userid']` or `$_SESSION['logged_in']` — doing so will break inter-module communication.

3. The Templates (Copy these into your folders)
----------------------------------------------

A. Database Connection Template
--------------------------------
Create a file at `<your_role>/config/db.php` and use this exact code. If your local XAMPP has a password, change it locally but do NOT push credentials to GitHub.

```php
<?php
// Template: db.php
$db_host = "localhost";
$db_user = "root";       
$db_pass = "";           // CHANGE LOCALLY IF NEEDED, BUT KEEP EMPTY ON GITHUB
$db_name = "job_portal_db"; 

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
```

B. Session & RBAC Template
--------------------------
Create a file at `<your_role>/helpers/session.php`. Include this at the top of all protected views (dashboards, admin pages) to secure them.

```php
<?php
// Template: session.php
// 1. Start the session safely (prevents "Headers Already Sent" errors)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. Standard Login Check
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}
// 3. Role-Based Access Control (RBAC)
function require_role($required_role) {
    if (!is_logged_in()) {
        // Redirect to your specific login page
        header("Location: ../views/login.php"); 
        exit();
    }
    if ($_SESSION['role'] !== $required_role) {
        die("Access Denied: You do not have permission to view this page.");
    }
}
?>
```

Example usage for a dashboard (`seeker/views/dashboard.php`):

```php
<?php
require_once '../helpers/session.php';
require_role('seeker'); // Secures the page!
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <!-- 1. Shared Global CSS -->
    <link rel="stylesheet" href="../../public/css/style.css">
    <!-- 2. Your Specific CSS -->
    <link rel="stylesheet" href="../../public/css/seeker/dashboard.css">
</head>
<body>
    <h1>Welcome User #<?php echo $_SESSION['user_id']; ?></h1>
</body>
</html>
```

4. Git Workflow Rules
---------------------
- Never push directly to `main`.
- Work on your own branch, e.g., `feature/seeker-module`.
- Commit often with descriptive messages (e.g., "Added employer job posting form").
- Push your branch and open a Pull Request when ready for review.
- If you modify the shared `public/css/style.css`, announce it to the team so others can pull changes to avoid conflicts.
