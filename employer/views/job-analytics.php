<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiring Analytics: <?php echo htmlspecialchars($job['title']); ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>

<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=list">Manage Jobs</a>
            <a href="../controllers/ApplicationController.php?action=shortlisted">Shortlisted</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1>Hiring Analytics: <?php echo htmlspecialchars($job['title']); ?></h1>
                    <p>Job posted on <?php echo date('M d, Y', strtotime($job['created_at'])); ?></p>
                </div>
                <a href="../controllers/JobController.php?action=list" class="btn-secondary"
                    style="text-decoration: none;">&larr; Back to Jobs</a>
            </div>

            <h2 style="margin-top: 2rem;">Application Funnel</h2>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <div class="card" style="flex: 1; text-align: center; background-color: #f8f9fa;">
                    <h3>Total Applications</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0; color: #333;">
                        <?php echo $funnel['total']; ?></p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #e3f2fd;">
                    <h3>Reviewed</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0; color: #0d47a1;">
                        <?php echo $funnel['reviewed']; ?></p>
                    <p style="font-size: 0.9rem; color: #666;">
                        <?php echo $funnel['total'] > 0 ? round(($funnel['reviewed'] / $funnel['total']) * 100) : 0; ?>%
                        conversion</p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #fff3e0;">
                    <h3>Shortlisted</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0; color: #e65100;">
                        <?php echo $funnel['shortlisted']; ?></p>
                    <p style="font-size: 0.9rem; color: #666;">
                        <?php echo $funnel['reviewed'] > 0 ? round(($funnel['shortlisted'] / $funnel['reviewed']) * 100) : 0; ?>%
                        conversion</p>
                </div>
                <div class="card" style="flex: 1; text-align: center; background-color: #e8f5e9;">
                    <h3>Interviewed</h3>
                    <p style="font-size: 2rem; font-weight: bold; margin: 1rem 0; color: #1b5e20;">
                        <?php echo $funnel['interview']; ?></p>
                    <p style="font-size: 0.9rem; color: #666;">
                        <?php echo $funnel['shortlisted'] > 0 ? round(($funnel['interview'] / $funnel['shortlisted']) * 100) : 0; ?>%
                        conversion</p>
                </div>
            </div>

            <h2 style="margin-top: 3rem;">Applications Over Time (Last 14 Days)</h2>
            <?php if (empty($timeline)): ?>
                <p>No application data available for the last 14 days.</p>
            <?php else: ?>
                <table style="width: 50%; border-collapse: collapse; margin-top: 1rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 0.5rem;">Date</th>
                            <th style="padding: 0.5rem;">Number of Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timeline as $date => $count): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 0.5rem;"><?php echo date('M d, Y', strtotime($date)); ?></td>
                                <td style="padding: 0.5rem;"><strong><?php echo $count; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>