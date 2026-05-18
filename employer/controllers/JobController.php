<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'index';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    $salary_min = !empty($_POST['salary_min']) ? $_POST['salary_min'] : null;
    $salary_max = !empty($_POST['salary_max']) ? $_POST['salary_max'] : null;
    $location = trim($_POST['location'] ?? '');
    $job_type = $_POST['job_type'] ?? 'full-time';
    $experience_level = $_POST['experience_level'] ?? 'entry';
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $status = $_POST['status'] ?? 'active';

    if ($title && $category_id && $description) {
        $stmt = $conn->prepare('
            INSERT INTO jobs (employer_id, category_id, title, description, requirements, benefits, salary_min, salary_max, location, job_type, experience_level, deadline, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->bind_param('iissssddsssss', 
            $employer_id, $category_id, $title, $description, $requirements, $benefits, 
            $salary_min, $salary_max, $location, $job_type, $experience_level, $deadline, $status
        );

        if ($stmt->execute()) {
            $success = "Job posted successfully!";
        } else {
            $error = "Failed to post job: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}

switch ($action) {
    case 'create':
        // Seed categories if empty
        $res = $conn->query('SELECT COUNT(*) as count FROM categories');
        $row = $res->fetch_assoc();
        if ($row['count'] == 0) {
            $default_categories = ['Technology', 'Healthcare', 'Finance', 'Marketing', 'Education', 'Engineering'];
            $stmt = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
            foreach ($default_categories as $cat_name) {
                $stmt->bind_param('s', $cat_name);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Fetch categories for the dropdown
        $categories = [];
        $res = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        require '../views/create-job.php';
        break;
    case 'list':
        $employer_id = $_SESSION['user_id'];
        $jobs = [];
        $stmt = $conn->prepare('
            SELECT id, title, status, deadline, created_at,
            (SELECT COUNT(*) FROM applications WHERE job_id = jobs.id) as app_count 
            FROM jobs 
            WHERE employer_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        require '../views/manage-jobs.php';
        break;
    case 'analytics':
        $job_id = $_GET['id'] ?? 0;
        $employer_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT title, created_at FROM jobs WHERE id = ? AND employer_id = ?");
        $stmt->bind_param('ii', $job_id, $employer_id);
        $stmt->execute();
        $job_result = $stmt->get_result();
        if ($job_result->num_rows === 0) {
            die("Job not found or access denied.");
        }
        $job = $job_result->fetch_assoc();
        $stmt->close();

        // Application Funnel counts
        $funnel = [
            'total' => 0,
            'reviewed' => 0,
            'shortlisted' => 0,
            'interview' => 0,
            'rejected' => 0
        ];

        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM applications WHERE job_id = ? GROUP BY status");
        $stmt->bind_param('i', $job_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $funnel['total'] += $row['count'];
            if (isset($funnel[$row['status']])) {
                $funnel[$row['status']] = $row['count'];
            }
        }
        $stmt->close();

        // Applications over time (last 14 days)
        $timeline = [];
        $stmt = $conn->prepare("
            SELECT DATE(applied_at) as apply_date, COUNT(*) as count 
            FROM applications 
            WHERE job_id = ? AND applied_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            GROUP BY DATE(applied_at)
            ORDER BY apply_date ASC
        ");
        $stmt->bind_param('i', $job_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $timeline[$row['apply_date']] = $row['count'];
        }
        $stmt->close();

        require '../views/job-analytics.php';
        break;

    case 'index':
    default:
        // Redirect to dashboard for now
        header('Location: ../views/dashboard.php');
        break;
}
?>
