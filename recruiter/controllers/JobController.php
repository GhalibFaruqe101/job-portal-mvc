<?php
// Recruiter Module: Job Controller
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/JobModel.php';
require_once '../models/ClientModel.php';

$jobModel = new JobModel($conn);
$clientModel = new ClientModel($conn);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreate($jobModel, $clientModel);
        break;
    case 'update':
        handleUpdate($jobModel, $clientModel);
        break;
    case 'delete':
        handleDelete($jobModel);
        break;
    default:
        header("Location: ../views/jobs.php");
        exit();
}

/**
 * Create a new job posting
 */
function handleCreate($jobModel, $clientModel)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/job_form.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $errors = validateJobForm();

    if (!empty($errors)) {
        $_SESSION['job_errors'] = $errors;
        $_SESSION['old_job'] = $_POST;
        header("Location: ../views/job_form.php");
        exit();
    }

    // Resolve employer_id from client
    $client_id = (int) ($_POST['client_id'] ?? 0);
    $employer_id = resolveEmployerId($clientModel, $client_id, $recruiter_id);

    $data = buildJobData($employer_id);

    if ($jobModel->createJob($recruiter_id, $data)) {
        $_SESSION['job_success'] = "Job posted successfully!";
        header("Location: ../views/jobs.php");
    } else {
        $_SESSION['job_errors'] = ["Failed to create job. Please try again."];
        $_SESSION['old_job'] = $_POST;
        header("Location: ../views/job_form.php");
    }
    exit();
}

/**
 * Update an existing job posting
 */
function handleUpdate($jobModel, $clientModel)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/jobs.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $job_id = (int) ($_POST['job_id'] ?? 0);
    $errors = validateJobForm();

    if (!$job_id) {
        $errors[] = "Invalid job ID.";
    }

    if (!empty($errors)) {
        $_SESSION['job_errors'] = $errors;
        $_SESSION['old_job'] = $_POST;
        header("Location: ../views/job_form.php?id=" . $job_id);
        exit();
    }

    $client_id = (int) ($_POST['client_id'] ?? 0);
    $employer_id = resolveEmployerId($clientModel, $client_id, $recruiter_id);
    $data = buildJobData($employer_id);

    if ($jobModel->updateJob($job_id, $recruiter_id, $data)) {
        $_SESSION['job_success'] = "Job updated successfully!";
    } else {
        $_SESSION['job_errors'] = ["Failed to update job."];
    }

    header("Location: ../views/jobs.php");
    exit();
}

/**
 * Delete a job posting
 */
function handleDelete($jobModel)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/jobs.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $job_id = (int) ($_POST['job_id'] ?? 0);

    if ($job_id && $jobModel->deleteJob($job_id, $recruiter_id)) {
        $_SESSION['job_success'] = "Job deleted successfully.";
    } else {
        $_SESSION['job_errors'] = ["Failed to delete job."];
    }

    header("Location: ../views/jobs.php");
    exit();
}

/**
 * Validate required job form fields
 */
function validateJobForm()
{
    $errors = [];
    if (empty(trim($_POST['title'] ?? '')))
        $errors[] = "Job title is required.";
    if (empty(trim($_POST['description'] ?? '')))
        $errors[] = "Job description is required.";
    if (empty($_POST['client_id'] ?? ''))
        $errors[] = "Please select a client.";
    if (empty($_POST['category_id'] ?? ''))
        $errors[] = "Please select a category.";
    if (empty($_POST['job_type'] ?? ''))
        $errors[] = "Job type is required.";
    if (empty($_POST['experience_level'] ?? ''))
        $errors[] = "Experience level is required.";

    $salary_min = floatval($_POST['salary_min'] ?? 0);
    $salary_max = floatval($_POST['salary_max'] ?? 0);
    if ($salary_min < 0)
        $errors[] = "Minimum salary cannot be negative.";
    if ($salary_max < 0)
        $errors[] = "Maximum salary cannot be negative.";
    if ($salary_max > 0 && $salary_min > $salary_max) {
        $errors[] = "Minimum salary cannot be greater than maximum salary.";
    }

    return $errors;
}

/**
 * Resolve employer_id from client record
 * For standalone clients, use recruiter's own user_id
 */
function resolveEmployerId($clientModel, $client_id, $recruiter_id)
{
    if ($client_id) {
        $client = $clientModel->getClientById($client_id, $recruiter_id);
        if ($client && $client['employer_id']) {
            return (int) $client['employer_id'];
        }
    }
    // Standalone or fallback: use recruiter's own ID
    return $recruiter_id;
}

/**
 * Build job data array from POST
 */
function buildJobData($employer_id)
{
    return [
        'employer_id' => $employer_id,
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'requirements' => trim($_POST['requirements'] ?? ''),
        'benefits' => trim($_POST['benefits'] ?? ''),
        'salary_min' => floatval($_POST['salary_min'] ?? 0),
        'salary_max' => floatval($_POST['salary_max'] ?? 0),
        'location' => trim($_POST['location'] ?? ''),
        'job_type' => $_POST['job_type'] ?? 'full-time',
        'experience_level' => $_POST['experience_level'] ?? 'entry',
        'deadline' => $_POST['deadline'] ?? null,
        'status' => $_POST['status'] ?? 'active',
    ];
}
?>