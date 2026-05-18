<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ClientModel.php';

$model = new ClientModel($conn);
$recruiter_id = $_SESSION['user_id'];

// Flash messages
$success = $_SESSION['client_success'] ?? '';
$error   = $_SESSION['client_error']   ?? '';
unset($_SESSION['client_success'], $_SESSION['client_error']);

$search  = trim($_GET['search'] ?? '');
$clients = $model->getMyClients($recruiter_id, $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Clients - JobPortal Recruiter</title>
    <meta name="description" content="Manage your client companies as a recruiter.">
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
            <h1>My Clients</h1>
            <p>Manage the companies you recruit for.</p>
        </div>
        <button class="btn-add-client" onclick="document.getElementById('addClientModal').style.display='flex'">+ Add Client</button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><span>✅</span> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><span>⚠️</span> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Search Bar -->
    <form method="GET" action="clients.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search your clients..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-filter">Search</button>
    </form>

    <!-- Client Cards -->
    <div class="clients-grid">
        <?php if (empty($clients)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏢</div>
                <p>No clients yet<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
                <p style="margin-top:0.5rem;font-size:0.9rem;">Click "+ Add Client" to get started.</p>
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
                            <p>
                                <span class="client-type-badge <?php echo $c['client_type']; ?>">
                                    <?php echo $c['client_type'] === 'linked' ? '🔗 Linked' : '📝 Standalone'; ?>
                                </span>
                                <?php if (!empty($c['industry'])): ?>
                                    &middot; <?php echo htmlspecialchars($c['industry']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="client-info">
                        <?php if (!empty($c['contact_name'])): ?>
                        <p>
                            <span class="info-icon">👤</span>
                            <strong>Contact:</strong> <?php echo htmlspecialchars($c['contact_name']); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($c['email'])): ?>
                        <p>
                            <span class="info-icon">✉️</span>
                            <a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" style="color: inherit; text-decoration: none;">
                                <?php echo htmlspecialchars($c['email']); ?>
                            </a>
                        </p>
                        <?php endif; ?>
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
                        <form method="POST" action="../controllers/ClientController.php?action=remove"
                              onsubmit="return confirm('Remove this client?');" style="display:inline;">
                            <?php echo csrfInput(); ?>
                            <input type="hidden" name="client_id" value="<?php echo $c['client_id']; ?>">
                            <button type="submit" class="btn-remove">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Add Client Modal -->
<div id="addClientModal" class="modal-overlay" style="display:none;">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Add Client</h2>
            <button class="modal-close" onclick="document.getElementById('addClientModal').style.display='none'">&times;</button>
        </div>

        <!-- Tab Switcher -->
        <div class="tab-switcher">
            <button class="tab-btn active" onclick="switchTab('linked', this)">🔗 Link Employer</button>
            <button class="tab-btn" onclick="switchTab('standalone', this)">📝 Standalone Company</button>
        </div>

        <!-- Tab: Link Employer -->
        <div id="tab-linked" class="tab-content active">
            <p class="tab-desc">Search for a registered employer to add as your client.</p>
            <div class="form-group">
                <label for="employer-search">Search Employers</label>
                <input type="text" id="employer-search" class="form-control"
                       placeholder="Type company name or email..." autocomplete="off">
            </div>
            <div id="employer-results" class="employer-results"></div>
            <form id="linkForm" method="POST" action="../controllers/ClientController.php?action=add_linked">
                <?php echo csrfInput(); ?>
                <input type="hidden" id="selected-employer-id" name="employer_id" value="">
                <div id="selected-employer" class="selected-employer" style="display:none;"></div>
                <button type="submit" class="btn-modal-submit" id="linkBtn" disabled>Link Client</button>
            </form>
        </div>

        <!-- Tab: Standalone Company -->
        <div id="tab-standalone" class="tab-content" style="display:none;">
            <p class="tab-desc">Add a company that is not registered on JobPortal.</p>
            <form method="POST" action="../controllers/ClientController.php?action=add_standalone">
                <?php echo csrfInput(); ?>
                <div class="form-group">
                    <label for="company-name">Company Name</label>
                    <input type="text" id="company-name" name="company_name" class="form-control"
                           placeholder="e.g. Acme Corp" required>
                </div>
                <button type="submit" class="btn-modal-submit">Add Client</button>
            </form>
        </div>
    </div>
</div>

<script>
// Tab switching
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).style.display = 'block';
    btn.classList.add('active');
}

// AJAX employer search
const searchInput = document.getElementById('employer-search');
const resultsDiv  = document.getElementById('employer-results');
const hiddenId    = document.getElementById('selected-employer-id');
const selectedDiv = document.getElementById('selected-employer');
const linkBtn     = document.getElementById('linkBtn');
let debounceTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    debounceTimer = setTimeout(() => {
        fetch('../api/search_employers.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="no-results">No employers found</div>';
                    return;
                }
                resultsDiv.innerHTML = data.map(emp =>
                    `<div class="employer-item" data-employer-id="${emp.employer_id}" data-company-name="${escapeHtml(emp.company_name || emp.contact_name)}" data-email="${escapeHtml(emp.email)}">
                        <div class="emp-name">${escapeHtml(emp.company_name || 'No company name')}</div>
                        <div class="emp-detail">${escapeHtml(emp.contact_name)} &middot; ${escapeHtml(emp.email)}</div>
                    </div>`
                ).join('');
            })
            .catch(() => {
                resultsDiv.innerHTML = '<div class="no-results">Search failed</div>';
            });
    }, 300);
});

// Event delegation for employer selection
resultsDiv.addEventListener('click', function(e) {
    const item = e.target.closest('.employer-item');
    if (item) {
        selectEmployer(
            parseInt(item.dataset.employerId),
            item.dataset.companyName,
            item.dataset.email
        );
    }
});

function selectEmployer(id, name, email) {
    hiddenId.value = id;
    selectedDiv.innerHTML = `<span class="selected-tag">🏢 ${escapeHtml(name)} (${escapeHtml(email)})</span>
                             <button type="button" class="btn-clear" onclick="clearSelection()">✕</button>`;
    selectedDiv.style.display = 'flex';
    resultsDiv.innerHTML = '';
    searchInput.value = '';
    linkBtn.disabled = false;
}

function clearSelection() {
    hiddenId.value = '';
    selectedDiv.style.display = 'none';
    selectedDiv.innerHTML = '';
    linkBtn.disabled = true;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

</body>
</html>
