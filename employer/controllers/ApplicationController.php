<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'list';
$employer_id = $_SESSION['user_id'];

switch ($action) {
    case 'job_applications':
        $job_id = $_GET['job_id'] ?? 0;
        
        // Verify job belongs to employer
        $stmt = $conn->prepare("SELECT title FROM jobs WHERE id = ? AND employer_id = ?");
        $stmt->bind_param('ii', $job_id, $employer_id);
        $stmt->execute();
        $job_result = $stmt->get_result();
        if ($job_result->num_rows === 0) {
            die("Job not found or access denied.");
        }
        $job = $job_result->fetch_assoc();
        $stmt->close();

        // Fetch applications
        $applications = [];
        $stmt = $conn->prepare("
            SELECT a.id, a.status, a.applied_at, u.name as seeker_name, u.email as seeker_email
            FROM applications a
            JOIN users u ON a.seeker_id = u.id
            WHERE a.job_id = ?
            ORDER BY a.applied_at DESC
        ");
        $stmt->bind_param('i', $job_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $applications[] = $row;
        }
        $stmt->close();

        require '../views/job-applications.php';
        break;

    case 'view_applicant':
        $app_id = $_GET['id'] ?? 0;

        // Fetch application details & verify employer owns the job
        $stmt = $conn->prepare("
            SELECT a.id as application_id, a.cover_letter, a.resume_path, a.status, a.applied_at, 
                   u.id as seeker_id, u.name, u.email, u.phone,
                   sp.headline, sp.summary, sp.skills, sp.years_experience, sp.education_level, sp.current_salary, sp.expected_salary, sp.preferred_location, sp.resume_path as profile_resume,
                   j.title as job_title, j.id as job_id
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u ON a.seeker_id = u.id
            LEFT JOIN seeker_profiles sp ON u.id = sp.user_id
            WHERE a.id = ? AND j.employer_id = ?
        ");
        $stmt->bind_param('ii', $app_id, $employer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            die("Application not found or access denied.");
        }
        $application = $result->fetch_assoc();
        $stmt->close();

        // Fetch messages for this application
        $messages = [];
        $stmt = $conn->prepare("
            SELECT sender_id, body, sent_at 
            FROM messages 
            WHERE application_id = ? 
            ORDER BY sent_at ASC
        ");
        $stmt->bind_param('i', $app_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $messages[] = $row;
        }
        $stmt->close();

        require '../views/applicant-profile.php';
        break;

    case 'update_status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $app_id = $_POST['application_id'] ?? 0;
            $new_status = $_POST['status'] ?? '';
            
            $allowed_statuses = ['submitted', 'reviewed', 'shortlisted', 'interview', 'rejected'];
            if (!in_array($new_status, $allowed_statuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }

            // Verify ownership
            $stmt = $conn->prepare("
                SELECT a.id FROM applications a 
                JOIN jobs j ON a.job_id = j.id 
                WHERE a.id = ? AND j.employer_id = ?
            ");
            $stmt->bind_param('ii', $app_id, $employer_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            $stmt->close();

            // Update status
            $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $new_status, $app_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            $stmt->close();
            exit;
        }
        break;

    case 'send_message':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $app_id = $_POST['application_id'] ?? 0;
            $recipient_id = $_POST['seeker_id'] ?? 0;
            $body = trim($_POST['body'] ?? '');

            if ($body) {
                $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, application_id, body) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('iiis', $employer_id, $recipient_id, $app_id, $body);
                $stmt->execute();
                $stmt->close();
            }
            header("Location: ?action=view_applicant&id=" . $app_id);
            exit;
        }
        break;

    case 'shortlisted':
        $candidates = [];
        $stmt = $conn->prepare("
            SELECT a.id as application_id, a.applied_at, u.name as seeker_name, u.email as seeker_email, j.title as job_title, j.id as job_id
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u ON a.seeker_id = u.id
            WHERE j.employer_id = ? AND a.status = 'shortlisted'
            ORDER BY a.applied_at DESC
        ");
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $candidates[] = $row;
        }
        $stmt->close();

        require '../views/shortlisted-candidates.php';
        break;

    default:
        header('Location: ../views/dashboard.php');
        break;
}
