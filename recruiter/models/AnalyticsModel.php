<?php
// Recruiter Module: Analytics Model
class AnalyticsModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get high-level KPI stats for the recruiter
     */
    public function getOverviewStats($recruiter_id) {
        // Total Active Jobs
        $jobsStmt = $this->conn->prepare("SELECT COUNT(*) as cnt FROM jobs WHERE recruiter_id = ? AND status = 'active'");
        $jobsStmt->bind_param("i", $recruiter_id);
        $jobsStmt->execute();
        $total_jobs = $jobsStmt->get_result()->fetch_assoc()['cnt'];

        // Total Candidates & Hired
        $candStmt = $this->conn->prepare("
            SELECT 
                COUNT(a.id) as total_candidates,
                SUM(CASE WHEN a.status = 'hired' THEN 1 ELSE 0 END) as total_hired
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            WHERE j.recruiter_id = ?
        ");
        $candStmt->bind_param("i", $recruiter_id);
        $candStmt->execute();
        $candData = $candStmt->get_result()->fetch_assoc();

        // Total Clients (Linked + Standalone)
        $clientStmt = $this->conn->prepare("SELECT COUNT(*) as cnt FROM recruiter_clients WHERE recruiter_id = ?");
        $clientStmt->bind_param("i", $recruiter_id);
        $clientStmt->execute();
        $total_clients = $clientStmt->get_result()->fetch_assoc()['cnt'];

        return [
            'total_jobs'       => $total_jobs,
            'total_candidates' => $candData['total_candidates'] ?? 0,
            'total_hired'      => $candData['total_hired'] ?? 0,
            'total_clients'    => $total_clients
        ];
    }

    /**
     * Get a list of all successful placements (status = 'hired')
     */
    public function getPlacements($recruiter_id, $limit = 50) {
        $stmt = $this->conn->prepare("
            SELECT 
                a.id AS application_id,
                a.applied_at,
                u.name AS seeker_name,
                u.email AS seeker_email,
                j.title AS job_title,
                COALESCE(ep.company_name, rc.company_name_override, 'Unknown') AS client_name
            FROM applications a
            JOIN users u ON a.seeker_id = u.id
            JOIN jobs j ON a.job_id = j.id
            LEFT JOIN recruiter_clients rc ON j.recruiter_id = rc.recruiter_id 
                AND (j.employer_id = rc.employer_id OR (rc.employer_id IS NULL AND j.employer_id = j.recruiter_id))
            LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
            WHERE j.recruiter_id = ? AND a.status = 'hired'
            ORDER BY a.applied_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $recruiter_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get performance per client (Jobs posted, Total Applications, Total Hired)
     */
    public function getClientReport($recruiter_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                COALESCE(ep.company_name, rc.company_name_override) AS client_name,
                rc.employer_id,
                COUNT(DISTINCT j.id) AS jobs_posted,
                COUNT(a.id) AS total_applications,
                SUM(CASE WHEN a.status = 'hired' THEN 1 ELSE 0 END) AS total_hired
            FROM recruiter_clients rc
            LEFT JOIN employer_profiles ep ON rc.employer_id = ep.user_id
            LEFT JOIN jobs j ON j.recruiter_id = rc.recruiter_id 
                AND (j.employer_id = rc.employer_id OR (rc.employer_id IS NULL AND j.employer_id = j.recruiter_id))
            LEFT JOIN applications a ON a.job_id = j.id
            WHERE rc.recruiter_id = ?
            GROUP BY rc.id
            ORDER BY total_hired DESC, jobs_posted DESC
        ");
        $stmt->bind_param("i", $recruiter_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
