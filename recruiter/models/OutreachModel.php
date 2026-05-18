<?php
// Recruiter Module: Outreach Model
class OutreachModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Send an outreach message to a seeker
     */
    public function sendOutreach($recruiter_id, $seeker_id, $job_id, $message) {
        // Early optimization check — real enforcement is via INSERT error handling
        $check = $this->conn->prepare(
            "SELECT id FROM recruiter_outreach 
             WHERE recruiter_id = ? AND seeker_id = ? AND job_id = ? 
             AND sent_at > DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        $check->bind_param("iii", $recruiter_id, $seeker_id, $job_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return -1; // Recently contacted for this job
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO recruiter_outreach (recruiter_id, seeker_id, job_id, message, status) 
             VALUES (?, ?, ?, ?, 'sent')"
        );
        $stmt->bind_param("iiis", $recruiter_id, $seeker_id, $job_id, $message);
        
        if (!$stmt->execute()) {
            // Handle duplicate key violation (e.g. from a race condition)
            if ($this->conn->errno === 1062) {
                return -1;
            }
            return false;
        }
        return true;
    }

    /**
     * Get all outreach messages sent by recruiter
     * Uses correlated subquery instead of GROUP BY to avoid duplicate rows
     */
    public function getMyOutreach($recruiter_id, $status_filter = '') {
        $sql = "SELECT ro.*, 
                       u.name AS seeker_name, u.email AS seeker_email,
                       j.title AS job_title,
                       COALESCE(ep.company_name, 
                           (SELECT rc.company_name_override FROM recruiter_clients rc 
                            WHERE rc.recruiter_id = j.recruiter_id 
                            AND (rc.employer_id = j.employer_id OR (rc.employer_id IS NULL AND j.employer_id = j.recruiter_id))
                            LIMIT 1),
                           'Unknown') AS client_name
                FROM recruiter_outreach ro
                JOIN users u ON ro.seeker_id = u.id
                JOIN jobs j ON ro.job_id = j.id
                LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                WHERE ro.recruiter_id = ?";

        $params = [$recruiter_id];
        $types  = 'i';

        if (!empty($status_filter)) {
            $sql .= " AND ro.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }

        $sql .= " ORDER BY ro.sent_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get basic stats for outreach
     */
    public function getOutreachStats($recruiter_id) {
        $stmt = $this->conn->prepare(
            "SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) AS read_count,
                SUM(CASE WHEN status = 'responded' THEN 1 ELSE 0 END) AS responded_count
             FROM recruiter_outreach
             WHERE recruiter_id = ?"
        );
        $stmt->bind_param("i", $recruiter_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
