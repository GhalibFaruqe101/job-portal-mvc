<?php
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ProfileModel.php';

$profileModel = new ProfileModel($conn);
$profile = $profileModel->getProfile($_SESSION['user_id']);

// Flash messages
$success = $_SESSION['profile_success'] ?? '';
$errors  = $_SESSION['profile_errors'] ?? [];
unset($_SESSION['profile_success'], $_SESSION['profile_errors']);

// Build avatar display
$avatar_src = !empty($profile['profile_pic'])
    ? '../../' . htmlspecialchars($profile['profile_pic'])
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - JobPortal Recruiter</title>
    <meta name="description" content="View and edit your Recruiter profile on JobPortal.">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/recruiter/dashboard.css">
    <link rel="stylesheet" href="../../public/css/recruiter/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal <span style="font-size:0.8rem;color:#8b5cf6;">[Recruiter]</span></a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="clients.php">Clients</a>
            <a href="candidates.php">Candidates</a>
            <a href="profile.php" class="active">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="profile-main">
        <h1>My Profile</h1>
        <p class="page-subtitle">Manage your personal information and agency details.</p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <!-- Avatar Banner -->
            <div class="profile-avatar-section">
                <form id="picForm" action="../controllers/ProfileController.php?action=update" method="POST" enctype="multipart/form-data">
                    <?php echo csrfInput(); ?>
                    <div class="avatar-wrapper">
                        <?php if ($avatar_src): ?>
                            <img src="<?php echo $avatar_src; ?>" alt="Profile Picture" class="avatar-img" id="avatarPreview">
                        <?php else: ?>
                            <div class="avatar-img" id="avatarPreview">🎯</div>
                        <?php endif; ?>
                        <label class="avatar-upload-label" for="profile_pic" title="Change photo">✏️</label>
                        <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                        <!-- Hidden fields required by controller -->
                        <input type="hidden" name="name"           value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>">
                        <input type="hidden" name="phone"          value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                        <input type="hidden" name="agency_name"    value="<?php echo htmlspecialchars($profile['agency_name'] ?? ''); ?>">
                        <input type="hidden" name="specialization" value="<?php echo htmlspecialchars($profile['specialization'] ?? ''); ?>">
                        <input type="hidden" name="description"    value="<?php echo htmlspecialchars($profile['description'] ?? ''); ?>">
                        <input type="hidden" name="website"        value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                    </div>
                    <button type="submit" id="uploadConfirmBtn" class="btn-save" style="display:none; margin-top:0.5rem; font-size:0.85rem; padding:0.4rem 1rem;">Upload Photo</button>
                </form>
                <div class="avatar-info">
                    <h2><?php echo htmlspecialchars($profile['name'] ?? 'Recruiter'); ?></h2>
                    <span class="role-tag">🎯 Recruiter</span>
                    <?php if (!empty($profile['agency_name'])): ?>
                        <p style="color:#e9d5ff; margin-top:0.5rem; font-size:0.9rem;">
                            <?php echo htmlspecialchars($profile['agency_name']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="profile-form-section">
                <form action="../controllers/ProfileController.php?action=update" method="POST" enctype="multipart/form-data">
                    <?php echo csrfInput(); ?>

                    <!-- Personal Info -->
                    <div class="form-section-title">Personal Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control"
                                   value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" disabled
                                   title="Email cannot be changed here.">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                   placeholder="01XXXXXXXXX"
                                   value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Agency Info -->
                    <div class="form-section-title">Agency Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="agency_name">Agency / Company Name *</label>
                            <input type="text" id="agency_name" name="agency_name" class="form-control"
                                   placeholder="TalentBridge Agency"
                                   value="<?php echo htmlspecialchars($profile['agency_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" id="specialization" name="specialization" class="form-control"
                                   placeholder="e.g. Tech, Finance, Healthcare"
                                   value="<?php echo htmlspecialchars($profile['specialization'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" class="form-control"
                                   placeholder="https://youragency.com"
                                   value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-grid full-width">
                        <div class="form-group">
                            <label for="description">About / Bio</label>
                            <textarea id="description" name="description" class="form-control"
                                      placeholder="Describe your agency and recruiting expertise..."><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="profile-submit-row">
                        <a href="dashboard.php" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<script>
document.getElementById('profile_pic').addEventListener('change', function() {
    const file = this.files[0];
    const btn = document.getElementById('uploadConfirmBtn');
    if (!file) { btn.style.display = 'none'; return; }

    // Validate type
    const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowed.includes(file.type)) {
        alert('Only JPG, PNG, GIF, or WEBP images are allowed.');
        this.value = '';
        btn.style.display = 'none';
        return;
    }
    // Validate size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Profile picture must be under 2MB.');
        this.value = '';
        btn.style.display = 'none';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            // Replace div with img
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Profile Picture';
            img.className = 'avatar-img';
            img.id = 'avatarPreview';
            preview.parentNode.replaceChild(img, preview);
        }
    };
    reader.readAsDataURL(file);

    // Show confirm button
    btn.style.display = 'inline-block';
});
</script>

</body>
</html>
