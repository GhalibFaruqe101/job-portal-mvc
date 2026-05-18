<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/session.php';

require_role('employer');

$action = $_GET['action'] ?? 'index';
$employer_id = $_SESSION['user_id'];

switch ($action) {
    case 'index':
        $recruiters = [];
        $stmt = $conn->prepare("
            SELECT rc.id, rc.added_at, rc.company_name_override, u.id as recruiter_id, u.name, u.email, u.phone, rp.agency_name, rp.specialization, rp.website 
            FROM recruiter_clients rc 
            JOIN users u ON rc.recruiter_id = u.id 
            LEFT JOIN recruiter_profiles rp ON u.id = rp.user_id 
            WHERE rc.employer_id = ?
        ");
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $recruiters[] = $row;
        }
        $stmt->close();
        
        require '../views/recruiters.php';
        break;

    default:
        header('Location: ../views/dashboard.php');
        break;
}
?>
