<?php
// Recruiter Module: Client Controller
require_once '../helpers/session.php';
require_role('recruiter');
require_once '../config/db.php';
require_once '../models/ClientModel.php';

$model  = new ClientModel($conn);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add_linked':
        handleAddLinked($model);
        break;
    case 'add_standalone':
        handleAddStandalone($model);
        break;
    case 'remove':
        handleRemove($model);
        break;
    default:
        header("Location: ../views/clients.php");
        exit();
}

/**
 * Add a client linked to an existing employer account
 */
function handleAddLinked($model) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/clients.php");
        exit();
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['client_error'] = "Invalid security token.";
        header("Location: ../views/clients.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $employer_id  = (int)($_POST['employer_id'] ?? 0);

    if (!$employer_id) {
        $_SESSION['client_error'] = "Please select a valid employer.";
    } else {
        $result = $model->addLinkedClient($recruiter_id, $employer_id);
        if ($result === -1) {
            $_SESSION['client_error'] = "This employer is already in your client list.";
        } elseif ($result) {
            $_SESSION['client_success'] = "Client linked successfully!";
        } else {
            $_SESSION['client_error'] = "Failed to add client. Please try again.";
        }
    }

    header("Location: ../views/clients.php");
    exit();
}

/**
 * Add a standalone client (company name only)
 */
function handleAddStandalone($model) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/clients.php");
        exit();
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['client_error'] = "Invalid security token.";
        header("Location: ../views/clients.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $company_name = trim($_POST['company_name'] ?? '');

    if (empty($company_name)) {
        $_SESSION['client_error'] = "Company name is required.";
    } else {
        $result = $model->addStandaloneClient($recruiter_id, $company_name);
        if ($result === -1) {
            $_SESSION['client_error'] = "A client with this name already exists.";
        } elseif ($result) {
            $_SESSION['client_success'] = "Standalone client added successfully!";
        } else {
            $_SESSION['client_error'] = "Failed to add client. Please try again.";
        }
    }

    header("Location: ../views/clients.php");
    exit();
}

/**
 * Remove a client from recruiter's list
 */
function handleRemove($model) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/clients.php");
        exit();
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['client_error'] = "Invalid security token.";
        header("Location: ../views/clients.php");
        exit();
    }

    $recruiter_id = $_SESSION['user_id'];
    $client_id    = (int)($_POST['client_id'] ?? 0);

    if ($client_id && $model->removeClient($client_id, $recruiter_id)) {
        $_SESSION['client_success'] = "Client removed successfully.";
    } else {
        $_SESSION['client_error'] = "Failed to remove client.";
    }

    header("Location: ../views/clients.php");
    exit();
}
?>
