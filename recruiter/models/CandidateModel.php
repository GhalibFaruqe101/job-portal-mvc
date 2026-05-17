<?php
// Recruiter Module: Candidate Model
class CandidateModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get all applications with seeker and job info
     * Optionally filter by status or search by name/job title
     */
    public function getAllCandidates($search = '', $status = '') {
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
                    ep.company_name
                FROM applications a
                JOIN users u        ON a.seeker_id   = u.id
                JOIN jobs  j        ON a.job_id      = j.id
                LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                WHERE 1=1";

        $params = [];
        $types  = '';

        if (!empty($search)) {
            $sql    .= " AND (u.name LIKE ? OR j.title LIKE ?)";
            $like    = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types   .= 'ss';
        }
        if (!empty($status)) {
            $sql    .= " AND a.status = ?";
            $params[] = $status;
            $types   .= 's';
        }

        $sql .= " ORDER BY a.applied_at DESC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update application status
     */
    public function updateStatus($application_id, $status) {
        $allowed = ['submitted', 'reviewed', 'shortlisted', 'interview', 'rejected', 'withdrawn'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->conn->prepare(
            "UPDATE applications SET status = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $status, $application_id);
        return $stmt->execute();
    }

    /**
     * Count candidates grouped by status
     */
    public function getStatusCounts() {
        $result = $this->conn->query(
            "SELECT status, COUNT(*) as total FROM applications GROUP BY status"
        );
        $counts = [];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = $row['total'];
        }
        return $counts;
    }
}
?>
