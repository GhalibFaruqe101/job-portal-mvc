<?php
// Recruiter Module: Client Model
class ClientModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get recruiter's own clients from recruiter_clients table
     * Joins employer_profiles if linked, falls back to company_name_override
     */
    public function getMyClients($recruiter_id, $search = '') {
        $sql = "SELECT 
                    rc.id AS client_id,
                    rc.employer_id,
                    rc.company_name_override,
                    rc.added_at,
                    COALESCE(ep.company_name, rc.company_name_override) AS company_name,
                    ep.industry,
                    ep.company_size,
                    ep.website,
                    u.name AS contact_name,
                    u.email,
                    u.phone,
                    (SELECT COUNT(*) FROM jobs j WHERE j.recruiter_id = rc.recruiter_id 
                        AND j.employer_id = COALESCE(rc.employer_id, rc.recruiter_id)
                        AND j.status = 'active') AS active_jobs,
                    CASE WHEN rc.employer_id IS NOT NULL THEN 'linked' ELSE 'standalone' END AS client_type
                FROM recruiter_clients rc
                LEFT JOIN users u ON rc.employer_id = u.id
                LEFT JOIN employer_profiles ep ON rc.employer_id = ep.user_id
                WHERE rc.recruiter_id = ?";

        $params = [$recruiter_id];
        $types  = 'i';

        if (!empty($search)) {
            $sql .= " AND (COALESCE(ep.company_name, rc.company_name_override) LIKE ? OR u.name LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        $sql .= " ORDER BY rc.added_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a client linked to an existing employer account
     */
    public function addLinkedClient($recruiter_id, $employer_id) {
        // Check if already linked
        $check = $this->conn->prepare(
            "SELECT id FROM recruiter_clients WHERE recruiter_id = ? AND employer_id = ?"
        );
        $check->bind_param("ii", $recruiter_id, $employer_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return -1; // Duplicate
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO recruiter_clients (recruiter_id, employer_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $recruiter_id, $employer_id);
        return $stmt->execute();
    }

    /**
     * Add a standalone client (not linked to any employer account)
     */
    public function addStandaloneClient($recruiter_id, $company_name) {
        // Check duplicate name for this recruiter
        $check = $this->conn->prepare(
            "SELECT id FROM recruiter_clients WHERE recruiter_id = ? AND company_name_override = ? AND employer_id IS NULL"
        );
        $check->bind_param("is", $recruiter_id, $company_name);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return -1; // Duplicate
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO recruiter_clients (recruiter_id, employer_id, company_name_override) VALUES (?, NULL, ?)"
        );
        $stmt->bind_param("is", $recruiter_id, $company_name);
        return $stmt->execute();
    }

    /**
     * Remove a client record (must belong to the recruiter)
     */
    public function removeClient($client_id, $recruiter_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM recruiter_clients WHERE id = ? AND recruiter_id = ?"
        );
        $stmt->bind_param("ii", $client_id, $recruiter_id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Search employer accounts for linking (AJAX autocomplete)
     */
    public function searchEmployers($query) {
        $like = '%' . $query . '%';
        $stmt = $this->conn->prepare(
            "SELECT u.id AS employer_id, u.name AS contact_name, u.email,
                    ep.company_name, ep.industry
             FROM users u
             LEFT JOIN employer_profiles ep ON u.id = ep.user_id
             WHERE u.role = 'employer' AND u.is_active = 1
               AND (ep.company_name LIKE ? OR u.name LIKE ? OR u.email LIKE ?)
             ORDER BY ep.company_name ASC
             LIMIT 10"
        );
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a single client by ID (must belong to recruiter)
     */
    public function getClientById($client_id, $recruiter_id) {
        $stmt = $this->conn->prepare(
            "SELECT rc.*, 
                    COALESCE(ep.company_name, rc.company_name_override) AS company_name,
                    ep.industry, ep.website, u.name AS contact_name, u.email
             FROM recruiter_clients rc
             LEFT JOIN users u ON rc.employer_id = u.id
             LEFT JOIN employer_profiles ep ON rc.employer_id = ep.user_id
             WHERE rc.id = ? AND rc.recruiter_id = ?"
        );
        $stmt->bind_param("ii", $client_id, $recruiter_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all employer clients (legacy — browse all employers)
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
