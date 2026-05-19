<?php
// Main Entry Point - JobPortal Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Your Career Path - JobPortal</title>
    
    <!-- Google Fonts for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/landing.css">
</head>
<body class="landing-page">

    <!-- Navigation -->
    <nav class="landing-nav">
        <a href="index.php" class="logo">JobPortal</a>
    </nav>

    <!-- Hero Content -->
    <main class="hero-section">
        <h1 class="hero-title">Secure Your Career Path<br>You're Worth It.</h1>
        <p class="hero-subtitle">Empowering ambitious job seekers and top-tier companies with comprehensive tools to discover, apply, and secure the perfect match. Select your portal to begin.</p>
    </main>

    <!-- Role Selection Grid -->
    <div class="roles-wrapper">
        <div class="roles-grid">
            
            <!-- Seeker Card -->
            <a href="seeker/index.php?action=login" class="role-card">
                <div class="role-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h2 class="role-name">Candidates</h2>
                <p class="role-desc">Search through thousands of job listings, apply instantly with your saved profile, and track your applications seamlessly.</p>
            </a>

            <!-- Employer Card (Disabled) -->
            <a href="#" class="role-card disabled" onclick="return false;">
                <div class="role-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h2 class="role-name">Employers</h2>
                <p class="role-desc">Post jobs directly, manage your company profile, and review applicants. Our employer suite is launching in the next phase.</p>
            </a>

            <!-- Recruiter Card -->
            <a href="recruiter/views/login.php" class="role-card">
                <div class="role-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="role-name">Agencies & Recruiters</h2>
                <p class="role-desc">Manage multiple client companies, post jobs on their behalf, and proactively source top talent from our candidate pool.</p>
            </a>

            <!-- Admin Card -->
            <a href="admin/index.php?action=login" class="role-card">
                <div class="role-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h2 class="role-name">Platform Admin</h2>
                <p class="role-desc">Access the central command to oversee platform operations, verify users, manage system policies, and resolve disputes.</p>
            </a>

        </div>
    </div>

</body>
</html>
