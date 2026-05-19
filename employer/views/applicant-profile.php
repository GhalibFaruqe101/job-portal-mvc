<?php
require_once '../helpers/session.php';
require_role('employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile - Job Portal</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/employer/dashboard.css">
</head>
<body>
    <nav class="global-nav">
        <a href="dashboard.php" class="logo">JobPortal</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../controllers/JobController.php?action=create">Post a Job</a>
            <a href="../controllers/ApplicationController.php?action=shortlisted">Shortlisted</a>
            <a href="../controllers/ProfileController.php?action=show">Profile</a>
            <a href="../controllers/RecruiterController.php?action=index">Recruiters</a>
            <a href="../controllers/ComplaintController.php?action=index">Complaints</a>
            <a href="../controllers/LogoutController.php">Logout</a>
        </div>
    </nav>

    <main style="padding: 2rem 5%; display: flex; gap: 2rem; align-items: flex-start;">
        <!-- Left Side: Profile Information -->
        <div class="card" style="flex: 2; margin-top: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #eee; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($application['name']); ?></h1>
                    <p style="color: #6c757d; font-size: 1.1rem; font-weight: bold;"><?php echo htmlspecialchars($application['headline'] ?? 'Job Seeker'); ?></p>
                </div>
                <div style="text-align: right;">
                    <a href="../controllers/ComplaintController.php?action=create&subject_id=<?php echo $application['seeker_id']; ?>" class="btn-primary" style="background-color: #dc3545; color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.9rem;">Report Applicant</a>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <h3 style="color: #6c757d; font-size: 0.9rem; text-transform: uppercase;">Contact Info</h3>
                    <p style="margin-top: 0.5rem;"><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo $application['phone'] ? htmlspecialchars($application['phone']) : 'N/A'; ?></p>
                </div>
                <div>
                    <h3 style="color: #6c757d; font-size: 0.9rem; text-transform: uppercase;">Professional Info</h3>
                    <p style="margin-top: 0.5rem;"><strong>Experience:</strong> <?php echo $application['years_experience'] !== null ? htmlspecialchars($application['years_experience']) . ' Years' : 'N/A'; ?></p>
                    <p><strong>Education:</strong> <?php echo $application['education_level'] ? htmlspecialchars($application['education_level']) : 'N/A'; ?></p>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="color: #6c757d; font-size: 0.9rem; text-transform: uppercase;">Summary & Biography</h3>
                <p style="margin-top: 0.5rem; line-height: 1.6; white-space: pre-line; background-color: #f8f9fa; padding: 1rem; border-radius: 4px;">
                    <?php echo $application['summary'] ? htmlspecialchars($application['summary']) : 'No profile summary provided.'; ?>
                </p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="color: #6c757d; font-size: 0.9rem; text-transform: uppercase;">Key Skills</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                    <?php 
                    if ($application['skills']) {
                        $skills = explode(',', $application['skills']);
                        foreach ($skills as $skill) {
                            echo '<span style="background-color: #e9ecef; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">' . htmlspecialchars(trim($skill)) . '</span>';
                        }
                    } else {
                        echo '<p style="color: #6c757d; font-style: italic;">No skills specified.</p>';
                    }
                    ?>
                </div>
            </div>

            <div style="margin-bottom: 2rem; border-top: 1px solid #eee; padding-top: 1.5rem;">
                <h2>Application Attachments</h2>
                
                <div style="margin-top: 1rem; background-color: #e2f0d9; padding: 1.5rem; border-radius: 4px; border: 1px solid #c5e0b4;">
                    <h3 style="color: #385723; margin-bottom: 0.5rem;">Cover Letter</h3>
                    <p style="line-height: 1.6; white-space: pre-line;">
                        <?php echo $application['cover_letter'] ? htmlspecialchars($application['cover_letter']) : 'No cover letter submitted.'; ?>
                    </p>
                </div>

                <div style="margin-top: 1.5rem; border: 1px solid #ddd; padding: 1.25rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; background-color: #fcfcfc;">
                    <div>
                        <h4 style="margin-bottom: 0.25rem;">Candidate Resume Attachment</h4>
                        <p style="font-size: 0.85rem; color: #6c757d;">
                            <?php 
                            $resume = $application['resume_path'] ?: $application['profile_resume'];
                            echo $resume ? 'Attachment: ' . basename($resume) : 'No resume uploaded.';
                            ?>
                        </p>
                    </div>
                    <?php if ($resume): ?>
                        <!-- Mocking a secure resume download path -->
                        <a href="../../<?php echo htmlspecialchars($resume); ?>" download class="btn-primary" style="text-decoration: none; padding: 0.6rem 1.2rem;">Download Resume</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Side: Real-Time Chat & Platform Messaging -->
        <div class="card" style="flex: 1.2; margin-top: 0; min-height: 500px; display: flex; flex-direction: column;">
            <h2 style="border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem;">Candidate Messaging</h2>
            <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 1rem;">Send interview invitations, update briefs, or rejection letters directly through the platform.</p>

            <div style="flex: 1; overflow-y: auto; background-color: #f8f9fa; border: 1px solid #eee; border-radius: 4px; padding: 1rem; margin-bottom: 1rem; max-height: 350px;">
                <?php if (empty($messages)): ?>
                    <p style="text-align: center; color: #6c757d; margin-top: 2rem; font-style: italic;">No message history yet. Start the conversation below!</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div style="margin-bottom: 1.25rem; max-width: 85%; <?php echo $msg['sender_id'] == $employer_id ? 'margin-left: auto; text-align: right;' : 'margin-right: auto;'; ?>">
                            <div style="display: inline-block; padding: 0.75rem 1rem; border-radius: 8px; text-align: left;
                                <?php echo $msg['sender_id'] == $employer_id ? 'background-color: #007bff; color: white;' : 'background-color: #e9ecef; color: #333;'; ?>">
                                <p style="margin: 0; font-size: 0.95rem;"><?php echo htmlspecialchars($msg['body']); ?></p>
                                <span style="font-size: 0.7rem; display: block; margin-top: 0.25rem; <?php echo $msg['sender_id'] == $employer_id ? 'color: #cce5ff;' : 'color: #6c757d;'; ?>">
                                    <?php echo date('M d, g:i a', strtotime($msg['sent_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="../controllers/ApplicationController.php?action=send_message" method="POST">
                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                <input type="hidden" name="seeker_id" value="<?php echo $application['seeker_id']; ?>">
                <textarea name="body" placeholder="Type a message to this candidate..." rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; resize: none; margin-bottom: 0.75rem; font-family: inherit;"></textarea>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; border: none; border-radius: 4px; cursor: pointer;">Send Message</button>
            </form>
        </div>
    </main>
</body>
</html>
