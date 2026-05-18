<?php
// Recruiter Module: Seeker Model
class SeekerModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Search seekers based on filters
     */
    public function searchSeekers($keyword = '', $exp_min = '', $location = '', $salary_max = '') {
        $sql = "SELECT u.id AS seeker_id, u.name, u.email, u.phone, u.profile_pic,
                       sp.headline, sp.skills, sp.years_experience, sp.expected_salary, sp.preferred_location
                FROM users u
                LEFT JOIN seeker_profiles sp ON u.id = sp.user_id
                WHERE u.role = 'seeker' AND u.is_active = 1";

        $params = [];
        $types  = '';

        if (!empty($keyword)) {
            $sql .= " AND (sp.skills LIKE ? ESCAPE '\\' OR sp.headline LIKE ? ESCAPE '\\' OR u.name LIKE ? ESCAPE '\\')";
            $keywordEscaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
            $like = '%' . $keywordEscaped . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $types .= 'sss';
        }

        if ($exp_min !== '') {
            $sql .= " AND sp.years_experience >= ?";
            $params[] = (int)$exp_min;
            $types .= 'i';
        }

        if (!empty($location)) {
            $sql .= " AND sp.preferred_location LIKE ? ESCAPE '\\'";
            $locEscaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $location);
            $params[] = '%' . $locEscaped . '%';
            $types .= 's';
        }

        if ($salary_max !== '') {
            $sql .= " AND (sp.expected_salary IS NULL OR sp.expected_salary <= ?)";
            $params[] = (float)$salary_max;
            $types .= 'd';
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT 50";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a seeker's public profile
     */
    public function getSeekerProfile($seeker_id) {
        $stmt = $this->conn->prepare(
            "SELECT u.id AS seeker_id, u.name, u.email, u.phone, u.profile_pic,
                    sp.headline, sp.summary, sp.skills, sp.years_experience, 
                    sp.education_level, sp.expected_salary, sp.preferred_location, sp.resume_path
             FROM users u
             LEFT JOIN seeker_profiles sp ON u.id = sp.user_id
             WHERE u.id = ? AND u.role = 'seeker'"
        );
        $stmt->bind_param("i", $seeker_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
