<?php
// Recruiter Module: Job Model
class JobModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Create a new job posting on behalf of a client
     */
    public function createJob($recruiter_id, $data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO jobs (employer_id, recruiter_id, category_id, title, description, 
                               requirements, benefits, salary_min, salary_max, location, 
                               job_type, experience_level, deadline, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiissssddsssss",
            $data['employer_id'],
            $recruiter_id,
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['requirements'],
            $data['benefits'],
            $data['salary_min'],
            $data['salary_max'],
            $data['location'],
            $data['job_type'],
            $data['experience_level'],
            $data['deadline'],
            $data['status']
        );
        return $stmt->execute();
    }

    /**
     * Get all jobs posted by this recruiter, with optional filters
     */
    public function getRecruiterJobs($recruiter_id, $client_id = '', $status = '', $category_id = '') {
        $sql = "SELECT j.*, 
                       c.name AS category_name,
                       COALESCE(ep.company_name, rc.company_name_override, 'Unknown') AS client_name,
                       rc.id AS client_id,
                       (SELECT COUNT(*) FROM applications WHERE job_id = j.id) AS app_count
                FROM jobs j
                LEFT JOIN categories c ON j.category_id = c.id
                LEFT JOIN recruiter_clients rc ON j.recruiter_id = rc.recruiter_id 
                    AND (j.employer_id = rc.employer_id OR (rc.employer_id IS NULL AND j.employer_id = j.recruiter_id))
                LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                WHERE j.recruiter_id = ?";

        $params = [$recruiter_id];
        $types  = 'i';

        if (!empty($client_id)) {
            $sql .= " AND rc.id = ?";
            $params[] = (int)$client_id;
            $types .= 'i';
        }
        if (!empty($status)) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if (!empty($category_id)) {
            $sql .= " AND j.category_id = ?";
            $params[] = (int)$category_id;
            $types .= 'i';
        }

        $sql .= " GROUP BY j.id ORDER BY j.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a single job (must belong to this recruiter)
     */
    public function getJobById($job_id, $recruiter_id) {
        $stmt = $this->conn->prepare(
            "SELECT j.*, c.name AS category_name
             FROM jobs j
             LEFT JOIN categories c ON j.category_id = c.id
             WHERE j.id = ? AND j.recruiter_id = ?"
        );
        $stmt->bind_param("ii", $job_id, $recruiter_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update an existing job
     */
    public function updateJob($job_id, $recruiter_id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE jobs SET employer_id = ?, category_id = ?, title = ?, description = ?,
                    requirements = ?, benefits = ?, salary_min = ?, salary_max = ?,
                    location = ?, job_type = ?, experience_level = ?, deadline = ?, status = ?
             WHERE id = ? AND recruiter_id = ?"
        );
        $stmt->bind_param(
            "iissssddsssssii",
            $data['employer_id'],
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['requirements'],
            $data['benefits'],
            $data['salary_min'],
            $data['salary_max'],
            $data['location'],
            $data['job_type'],
            $data['experience_level'],
            $data['deadline'],
            $data['status'],
            $job_id,
            $recruiter_id
        );
        return $stmt->execute();
    }

    /**
     * Delete a job (must belong to this recruiter)
     */
    public function deleteJob($job_id, $recruiter_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM jobs WHERE id = ? AND recruiter_id = ?"
        );
        $stmt->bind_param("ii", $job_id, $recruiter_id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Get all categories for dropdown
     */
    public function getCategories() {
        $result = $this->conn->query("SELECT id, name FROM categories ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
