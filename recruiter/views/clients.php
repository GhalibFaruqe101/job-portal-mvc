<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ClientModel.php';

$model = new ClientModel($conn);

$search = trim($_GET['search'] ?? '');
$clients = $model->getAllClients($search);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - JobPortal Recruiter</title>
    <meta name="description" content="Manage your employer clients.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/clients.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="global-nav">
    <a href="dashboard.php" class="logo">JobPortal <span style="font-size:0.8rem;color:#8b5cf6;">[Recruiter]</span></a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php" class="active">Clients</a>
        <a href="candidates.php">Candidates</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="clients-main">
    <div class="page-header">
        <div>
            <h1>Employer Clients</h1>
            <p>View all companies currently hiring through JobPortal.</p>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="clients.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search by company or contact name..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-filter">Search</button>
    </form>

    <div class="clients-grid">
        <?php if (empty($clients)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏢</div>
                <p>No clients found<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
            </div>
        <?php else: ?>
            <?php foreach ($clients as $c): ?>
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-logo">
                            <?php echo strtoupper(substr($c['company_name'] ?? 'C', 0, 1)); ?>
                        </div>
                        <div class="client-title">
                            <h2><?php echo htmlspecialchars($c['company_name'] ?? 'Unknown Company'); ?></h2>
                            <p><?php echo htmlspecialchars($c['industry'] ?? 'Industry not specified'); ?></p>
                        </div>
                    </div>
                    <div class="client-info">
                        <p>
                            <span class="info-icon">👤</span> 
                            <strong>Contact:</strong> <?php echo htmlspecialchars($c['contact_name']); ?>
                        </p>
                        <p>
                            <span class="info-icon">✉️</span> 
                            <a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" style="color: inherit; text-decoration: none;">
                                <?php echo htmlspecialchars($c['email']); ?>
                            </a>
                        </p>
                        <?php if (!empty($c['phone'])): ?>
                        <p>
                            <span class="info-icon">📞</span> 
                            <?php echo htmlspecialchars($c['phone']); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($c['website'])): ?>
                        <p>
                            <span class="info-icon">🌐</span> 
                            <a href="<?php echo htmlspecialchars($c['website']); ?>" target="_blank" style="color: #8b5cf6;">Website</a>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="client-footer">
                        <span class="active-jobs"><?php echo $c['active_jobs']; ?> Active Job<?php echo $c['active_jobs'] != 1 ? 's' : ''; ?></span>
                        <a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" class="btn-contact">Message</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
