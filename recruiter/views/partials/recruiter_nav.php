<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="global-nav">
    <a href="dashboard.php" class="logo">JobPortal <span style="font-size: 0.8rem; color: #8b5cf6;">[Recruiter]</span></a>
    <div class="nav-links">
        <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="clients.php" class="<?php echo $currentPage === 'clients.php' || $currentPage === 'client_report.php' ? 'active' : ''; ?>">Clients</a>
        <a href="jobs.php" class="<?php echo $currentPage === 'jobs.php' || $currentPage === 'job_form.php' ? 'active' : ''; ?>">Jobs</a>
        <a href="seekers.php" class="<?php echo $currentPage === 'seekers.php' || $currentPage === 'seeker_profile.php' ? 'active' : ''; ?>">Seekers</a>
        <a href="outreach.php" class="<?php echo $currentPage === 'outreach.php' ? 'active' : ''; ?>">Outreach</a>
        <a href="candidates.php" class="<?php echo $currentPage === 'candidates.php' ? 'active' : ''; ?>">Candidates</a>
        <a href="analytics.php" class="<?php echo $currentPage === 'analytics.php' || $currentPage === 'placements.php' ? 'active' : ''; ?>">Analytics</a>
        <a href="profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>
