<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'create';
$employer_id = $_SESSION['user_id'];

switch ($action) {
    case 'index':
        $complaints = [];
        $stmt = $conn->prepare("
            SELECT c.id, c.submitter_id, c.subject_id, c.description, c.status, c.admin_note, c.created_at, 
                   u.name as subject_name, u.role as subject_role 
            FROM complaints c
            JOIN users u ON c.subject_id = u.id
            WHERE c.submitter_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $complaints[] = $row;
        }
        $stmt->close();
        require '../views/complaints.php';
        break;

    case 'create':
        $subject_id = $_GET['subject_id'] ?? 0;
        
        // Fetch subject details to verify they exist and get their name
        $stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
        $stmt->bind_param('i', $subject_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            die("User not found.");
        }
        $subject = $res->fetch_assoc();
        $stmt->close();
        
        require '../views/complaint-form.php';
        break;

    case 'submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_id = $_POST['subject_id'] ?? 0;
            $description = trim($_POST['description'] ?? '');

            if ($subject_id && $description) {
                $stmt = $conn->prepare("INSERT INTO complaints (submitter_id, subject_id, description, status) VALUES (?, ?, ?, 'open')");
                $stmt->bind_param('iis', $employer_id, $subject_id, $description);
                if ($stmt->execute()) {
                    $success = "Your complaint has been submitted to the admin for review.";
                } else {
                    $error = "Failed to submit complaint.";
                }
                $stmt->close();
            } else {
                $error = "Description cannot be empty.";
            }

            // Refetch subject to re-render form
            $stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
            $stmt->bind_param('i', $subject_id);
            $subject = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            require '../views/complaint-form.php';
        }
        break;

    default:
        header('Location: ../views/dashboard.php');
        break;
}
