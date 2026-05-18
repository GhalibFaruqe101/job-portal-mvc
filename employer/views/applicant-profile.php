<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Profile: <?php echo htmlspecialchars($application['name']); ?></title>
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
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%;">
        <div style="display: flex; gap: 2rem;">
            
            <!-- Left Column: Application Details -->
            <div style="flex: 2;">
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h1><?php echo htmlspecialchars($application['name']); ?></h1>
                            <p style="color: #666; font-size: 1.1rem;"><?php echo htmlspecialchars($application['headline'] ?? 'No headline provided'); ?></p>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <a href="../controllers/ComplaintController.php?action=create&subject_id=<?php echo $application['seeker_id']; ?>" class="btn-secondary" style="text-decoration: none; color: #dc3545; border-color: #dc3545; background: transparent;">Report Applicant</a>
                            <a href="?action=job_applications&job_id=<?php echo $application['job_id']; ?>" class="btn-secondary" style="text-decoration: none;">&larr; Back to List</a>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; display: flex; gap: 2rem;">
                        <div>
                            <strong>Email:</strong><br>
                            <?php echo htmlspecialchars($application['email']); ?>
                        </div>
                        <div>
                            <strong>Phone:</strong><br>
                            <?php echo htmlspecialchars($application['phone'] ?? 'N/A'); ?>
                        </div>
                        <div>
                            <strong>Applied On:</strong><br>
                            <?php echo date('M d, Y', strtotime($application['applied_at'])); ?>
                        </div>
                        <div>
                            <strong>Current Status:</strong><br>
                            <span class="badge badge-primary"><?php echo ucfirst($application['status']); ?></span>
                        </div>
                    </div>

                    <hr style="margin: 2rem 0;">

                    <h2>Cover Letter</h2>
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 4px; border: 1px solid #eee; margin-top: 1rem; white-space: pre-wrap;"><?php echo htmlspecialchars($application['cover_letter'] ?? 'No cover letter provided.'); ?></div>

                    <hr style="margin: 2rem 0;">

                    <h2>Seeker Profile Details</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                        <div>
                            <strong>Skills:</strong>
                            <p><?php echo htmlspecialchars($application['skills'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <strong>Experience:</strong>
                            <p><?php echo htmlspecialchars($application['years_experience'] ?? '0'); ?> years</p>
                        </div>
                        <div>
                            <strong>Education:</strong>
                            <p><?php echo htmlspecialchars($application['education_level'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <strong>Location Preference:</strong>
                            <p><?php echo htmlspecialchars($application['preferred_location'] ?? 'N/A'); ?></p>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <strong>About:</strong>
                        <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($application['summary'] ?? 'No summary provided.'); ?></p>
                    </div>

                    <hr style="margin: 2rem 0;">

                    <h2>Resume</h2>
                    <?php 
                    $resume = $application['resume_path'] ?: $application['profile_resume'];
                    if ($resume): 
                    ?>
                        <p>The applicant has provided a resume document.</p>
                        <a href="../../<?php echo htmlspecialchars($resume); ?>" class="btn-primary" target="_blank" style="text-decoration: none; display: inline-block; margin-top: 0.5rem;">Download Resume</a>
                    <?php else: ?>
                        <p style="color: #666;">No resume document was attached to this application or profile.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Messaging -->
            <div style="flex: 1;">
                <div class="card">
                    <h2>Message Applicant</h2>
                    <p style="font-size: 0.9rem; color: #666;">Send interview invitations or updates directly to the candidate.</p>
                    
                    <div style="margin: 1.5rem 0; max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 1rem; border-radius: 4px; border: 1px solid #eee;">
                        <?php if (empty($messages)): ?>
                            <p style="color: #888; text-align: center; margin: 0;">No messages yet.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <div style="margin-bottom: 1rem; padding: 0.8rem; border-radius: 6px; <?php echo $msg['sender_id'] == $_SESSION['user_id'] ? 'background: #e3f2fd; margin-left: 2rem;' : 'background: #fff; border: 1px solid #ddd; margin-right: 2rem;'; ?>">
                                    <div style="font-size: 0.8rem; color: #666; margin-bottom: 0.3rem;">
                                        <?php echo $msg['sender_id'] == $_SESSION['user_id'] ? 'You' : htmlspecialchars($application['name']); ?> 
                                        &bull; <?php echo date('M d, g:i A', strtotime($msg['sent_at'])); ?>
                                    </div>
                                    <div style="white-space: pre-wrap; font-size: 0.95rem;"><?php echo htmlspecialchars($msg['body']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="?action=send_message">
                        <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                        <input type="hidden" name="seeker_id" value="<?php echo $application['seeker_id']; ?>">
                        <div class="form-group">
                            <textarea name="body" class="form-control" rows="3" placeholder="Type your message here..." required></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0.5rem;">Send Message</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
