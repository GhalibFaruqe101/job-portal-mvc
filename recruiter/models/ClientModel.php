<?php
// Recruiter Module: Client Model
class ClientModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get all employer clients with their profile info and active job count
     */
    public function getAllClients($search = '') {
        $sql = "SELECT 
                    u.id AS employer_id,
                    u.name AS contact_name,
                    u.email,
                    u.phone,
                    ep.company_name,
                    ep.industry,
                    ep.company_size,
                    ep.website,
                    (SELECT COUNT(*) FROM jobs WHERE employer_id = u.id AND status = 'active') AS active_jobs
                FROM users u
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE u.role = 'employer'";
        
        $params = [];
        $types  = '';

        if (!empty($search)) {
            $sql .= " AND (ep.company_name LIKE ? OR u.name LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        $sql .= " ORDER BY ep.company_name ASC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
?>
