<?php


require_once __DIR__ . '/../config/db.php';

class SeekerModel {

    //  AUTH 
    public function registerUser(string $name, string $email, string $phone, string $passwordHash): int|false {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO users (name, email, password_hash, phone, role, is_active, is_verified, created_at)
             VALUES (?, ?, ?, ?, 'seeker', 1, 0, NOW())"
        );
        $stmt->bind_param('ssss', $name, $email, $passwordHash, $phone);
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        return false;
    }

    public function getUserByEmail(string $email): array|null {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'seeker' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function getUserById(int $userId): array|null {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function emailExists(string $email): bool {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // ─── SEEKER PROFILE

    public function getSeekerProfile(int $userId): array|null {
        $db = getDB();
        
        $stmt = $db->prepare("SELECT * FROM seeker_profiles WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function upsertSeekerProfile(
        int $userId,
        string $headline,
        string $summary,
        string $skills,
        int $yearsExp,
        string $educationLevel,
        float $currentSalary,
        float $expectedSalary,
        string $preferredLocation,
        string $resumePath = ''
    ): bool {
        $db = getDB();
     
        $stmt = $db->prepare(
            "INSERT INTO seeker_profiles
                (user_id, headline, summary, skills, years_experience, education_level,
                 current_salary, expected_salary, preferred_location, resume_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                headline = VALUES(headline),
                summary = VALUES(summary),
                skills = VALUES(skills),
                years_experience = VALUES(years_experience),
                education_level = VALUES(education_level),
                current_salary = VALUES(current_salary),
                expected_salary = VALUES(expected_salary),
                preferred_location = VALUES(preferred_location),
                resume_path = IF(VALUES(resume_path) != '', VALUES(resume_path), resume_path)"
        );
        
        $stmt->bind_param('isssisddss', $userId, $headline, $summary, $skills, $yearsExp,
            $educationLevel, $currentSalary, $expectedSalary, $preferredLocation, $resumePath);
        return $stmt->execute();
    }

    public function updateProfilePic(int $userId, string $picPath): bool {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param('si', $picPath, $userId);
        return $stmt->execute();
    }

    public function updateResumePath(int $userId, string $resumePath): bool {
        $db = getDB();
        
        $stmt = $db->prepare(
            "INSERT INTO seeker_profiles (user_id, resume_path, headline, summary, skills, preferred_location) 
             VALUES (?, ?, '', '', '', '') 
             ON DUPLICATE KEY UPDATE resume_path = VALUES(resume_path)"
        );
        $stmt->bind_param('is', $userId, $resumePath);
        return $stmt->execute();
    }


    public function searchJobs(
        string $keyword = '',
        int $categoryId = 0,
        string $location = '',
        string $jobType = '',
        string $experienceLevel = '',
        float $salaryMin = 0,
        float $salaryMax = 0
    ): array {
        $db = getDB();

        $sql = "SELECT j.*, c.name AS category_name,
                       ep.company_name, ep.logo_path,
                       u.name AS poster_name,
                       rp.agency_name
                FROM jobs j
                LEFT JOIN categories c ON j.category_id = c.id
                LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN recruiter_profiles rp ON j.recruiter_id = rp.user_id
                WHERE j.status = 'active'";

        $params = [];
        $types  = '';

        if ($keyword !== '') {
            $kw = '%' . $keyword . '%';
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR ep.company_name LIKE ?)";
            $params[] = $kw; $params[] = $kw; $params[] = $kw;
            $types .= 'sss';
        }
        if ($categoryId > 0) {
            $sql .= " AND j.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        if ($location !== '') {
            $loc = '%' . $location . '%';
            $sql .= " AND j.location LIKE ?";
            $params[] = $loc;
            $types .= 's';
        }
        if ($jobType !== '') {
            $sql .= " AND j.job_type = ?";
            $params[] = $jobType;
            $types .= 's';
        }
        if ($experienceLevel !== '') {
            $sql .= " AND j.experience_level = ?";
            $params[] = $experienceLevel;
            $types .= 's';
        }
        if ($salaryMin > 0) {
            $sql .= " AND j.salary_max >= ?";
            $params[] = $salaryMin;
            $types .= 'd';
        }
        if ($salaryMax > 0) {
            $sql .= " AND j.salary_min <= ?";
            $params[] = $salaryMax;
            $types .= 'd';
        }

        $sql .= " ORDER BY j.is_featured DESC, j.created_at DESC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobById(int $jobId): array|null {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT j.*, c.name AS category_name,
                    ep.company_name, ep.logo_path, ep.description AS company_desc,
                    ep.website, ep.address, ep.industry,
                    u.name AS poster_name,
                    rp.agency_name
             FROM jobs j
             LEFT JOIN categories c ON j.category_id = c.id
             LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
             LEFT JOIN users u ON j.employer_id = u.id
             LEFT JOIN recruiter_profiles rp ON j.recruiter_id = rp.user_id
             WHERE j.id = ? AND j.status = 'active'
             LIMIT 1"
        );
        $stmt->bind_param('i', $jobId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function getCategories(): array {
        $db = getDB();
        $result = $db->query("SELECT * FROM categories ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ─── APPLICATIONS 
    public function hasApplied(int $seekerId, int $jobId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT id FROM applications WHERE job_id = ? AND seeker_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $jobId, $seekerId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function applyToJob(
        int $seekerId,
        int $jobId,
        string $coverLetter,
        string $resumePath
    ): int|false {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status, applied_at)
             VALUES (?, ?, ?, ?, 'submitted', NOW())"
        );
        $stmt->bind_param('iiss', $jobId, $seekerId, $coverLetter, $resumePath);
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        return false;
    }

    public function getApplicationsBySeeker(int $seekerId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT a.*, j.title AS job_title, j.location,
                    ep.company_name, ep.logo_path
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
             WHERE a.seeker_id = ?
             ORDER BY a.applied_at DESC"
        );
        $stmt->bind_param('i', $seekerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function withdrawApplication(int $applicationId, int $seekerId): bool {
        $db = getDB();
        // Only withdraw if status is still 'submitted'
        $stmt = $db->prepare(
            "UPDATE applications
             SET status = 'withdrawn'
             WHERE id = ? AND seeker_id = ? AND status = 'submitted'"
        );
        $stmt->bind_param('ii', $applicationId, $seekerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // ─── SAVED JOBS 

    public function getSavedJobs(int $userId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT sj.*, j.title, j.location, j.job_type, j.salary_min, j.salary_max,
                    j.deadline, j.status AS job_status,
                    ep.company_name, ep.logo_path
             FROM saved_jobs sj
             JOIN jobs j ON sj.job_id = j.id
             LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
             WHERE sj.user_id = ?
             ORDER BY sj.saved_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function isJobSaved(int $userId, int $jobId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $userId, $jobId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function saveJob(int $userId, int $jobId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT IGNORE INTO saved_jobs (user_id, job_id, saved_at) VALUES (?, ?, NOW())"
        );
        $stmt->bind_param('ii', $userId, $jobId);
        return $stmt->execute();
    }

    public function unsaveJob(int $userId, int $jobId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?"
        );
        $stmt->bind_param('ii', $userId, $jobId);
        return $stmt->execute();
    }

    // ─── JOB ALERTS

    public function getAlertsBySeeker(int $seekerId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT ja.*, c.name AS category_name
             FROM job_alerts ja
             LEFT JOIN categories c ON ja.category_id = c.id
             WHERE ja.seeker_id = ?
             ORDER BY ja.created_at DESC"
        );
        $stmt->bind_param('i', $seekerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createAlert(
        int $seekerId,
        string $keyword,
        int $categoryId = 0,
        string $location = '',
        string $jobType = ''
    ): bool {
        $db = getDB();
        $catId = $categoryId > 0 ? $categoryId : null;
        $stmt = $db->prepare(
            "INSERT INTO job_alerts (seeker_id, keyword, category_id, location, job_type, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param('isiss', $seekerId, $keyword, $catId, $location, $jobType);
        return $stmt->execute();
    }

    public function deleteAlert(int $alertId, int $seekerId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "DELETE FROM job_alerts WHERE id = ? AND seeker_id = ?"
        );
        $stmt->bind_param('ii', $alertId, $seekerId);
        return $stmt->execute();
    }

    // ─── MESSAGES 

    public function getInboxMessages(int $userId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT m.*, u.name AS sender_name, u.profile_pic AS sender_pic,
                    j.title AS job_title
             FROM messages m
             JOIN users u ON m.sender_id = u.id
             LEFT JOIN applications a ON m.application_id = a.id
             LEFT JOIN jobs j ON a.job_id = j.id
             WHERE m.recipient_id = ?
             ORDER BY m.sent_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function sendMessage(
        int $senderId,
        int $recipientId,
        int $applicationId,
        string $body
    ): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO messages (sender_id, recipient_id, application_id, body, sent_at, is_read)
             VALUES (?, ?, ?, ?, NOW(), 0)"
        );
        $stmt->bind_param('iiis', $senderId, $recipientId, $applicationId, $body);
        return $stmt->execute();
    }

    public function markMessageRead(int $messageId, int $recipientId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE messages SET is_read = 1 WHERE id = ? AND recipient_id = ?"
        );
        $stmt->bind_param('ii', $messageId, $recipientId);
        return $stmt->execute();
    }

    public function markAllMessagesRead(int $recipientId): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE messages SET is_read = 1 WHERE recipient_id = ? AND is_read = 0"
        );
        $stmt->bind_param('i', $recipientId);
        return $stmt->execute();
    }

    public function getRecruiterOutreach(int $seekerId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT ro.*, u.name AS recruiter_name, u.profile_pic,
                    rp.agency_name, j.title AS job_title
             FROM recruiter_outreach ro
             JOIN users u ON ro.recruiter_id = u.id
             LEFT JOIN recruiter_profiles rp ON ro.recruiter_id = rp.user_id
             LEFT JOIN jobs j ON ro.job_id = j.id
             WHERE ro.seeker_id = ?
             ORDER BY ro.sent_at DESC"
        );
        $stmt->bind_param('i', $seekerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateOutreachStatus(int $outreachId, int $seekerId, string $status): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE recruiter_outreach SET status = ? WHERE id = ? AND seeker_id = ?"
        );
        $stmt->bind_param('sii', $status, $outreachId, $seekerId);
        return $stmt->execute();
    }

    // ─── COMPLAINTS

    public function submitComplaint(int $submitterId, int $subjectId, string $description): bool {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO complaints (submitter_id, subject_id, description, status, created_at)
             VALUES (?, ?, ?, 'open', NOW())"
        );
        $stmt->bind_param('iis', $submitterId, $subjectId, $description);
        return $stmt->execute();
    }

    // ─── NOTIFICATIONS


    public function getMatchedAlertJobs(int $seekerId): array {
        $db = getDB();
        // Fetch seeker's alerts
        $alerts = $this->getAlertsBySeeker($seekerId);
        if (empty($alerts)) return [];

        $matched = [];
        foreach ($alerts as $alert) {
            $jobs = $this->searchJobs(
                $alert['keyword'],
                (int)($alert['category_id'] ?? 0),
                $alert['location'] ?? '',
                $alert['job_type'] ?? ''
            );
            foreach ($jobs as $job) {
                $matched[$job['id']] = $job; // deduplicate by job id
            }
        }
        return array_values($matched);
    }
}