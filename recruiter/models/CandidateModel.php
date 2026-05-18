<?php
// Recruiter Module: Candidate Model
class CandidateModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get all applications for jobs posted by this recruiter
     * Optionally filter by job_id, status, or search keyword
     */
    public function getRecruiterCandidates($recruiter_id, $job_id = '', $status = '', $search = '', $limit = 0) {
        $sql = "SELECT 
                    a.id           AS application_id,
                    a.status       AS app_status,
                    a.applied_at,
                    a.cover_letter,
                    a.resume_path,
                    u.id           AS seeker_id,
                    u.name         AS seeker_name,
                    u.email        AS seeker_email,
                    u.phone        AS seeker_phone,
                    u.profile_pic,
                    j.id           AS job_id,
                    j.title        AS job_title,
                    j.location,
                    COALESCE(ep.company_name, rc.company_name_override, 'Unknown') AS company_name
                FROM applications a
                JOIN users u        ON a.seeker_id   = u.id
                JOIN jobs  j        ON a.job_id      = j.id
                LEFT JOIN recruiter_clients rc ON j.recruiter_id = rc.recruiter_id 
                    AND (j.employer_id = rc.employer_id OR (rc.employer_id IS NULL AND j.employer_id = j.recruiter_id))
                LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                WHERE j.recruiter_id = ?";

        $params = [$recruiter_id];
        $types  = 'i';

        if (!empty($job_id)) {
            $sql .= " AND a.job_id = ?";
            $params[] = (int)$job_id;
            $types .= 'i';
        }
        if (!empty($status)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if (!empty($search)) {
            $sql .= " AND (u.name LIKE ? OR j.title LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        $sql .= " GROUP BY a.id ORDER BY a.applied_at DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update application status (must belong to recruiter's job)
     */
    public function updateStatus($application_id, $status, $recruiter_id) {
        $allowed = ['submitted', 'reviewed', 'shortlisted', 'interview', 'rejected', 'withdrawn', 'hired'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->conn->prepare(
            "UPDATE applications a
             JOIN jobs j ON a.job_id = j.id
             SET a.status = ? 
             WHERE a.id = ? AND j.recruiter_id = ?"
        );
        $stmt->bind_param("sii", $status, $application_id, $recruiter_id);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    /**
     * Count candidates grouped by status for this recruiter
     */
    public function getStatusCounts($recruiter_id) {
        $stmt = $this->conn->prepare(
            "SELECT a.status, COUNT(a.id) as total 
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             WHERE j.recruiter_id = ?
             GROUP BY a.status"
        );
        $stmt->bind_param("i", $recruiter_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $counts = [];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = $row['total'];
        }
        return $counts;
    }
}
?>
