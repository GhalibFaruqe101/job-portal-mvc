<?php

require_once __DIR__ . '/../config/db.php';

class AdminModel {
    private mysqli $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    private function stmt(string $sql, string $types = '', array $params = []): mysqli_stmt {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('SQL prepare failed: ' . $this->conn->error);
        }
        if ($types !== '') {
            $refs = [];
            foreach ($params as $key => $value) {
                $refs[$key] = $value;
            }
            $bind = [$types];
            foreach ($refs as $key => &$value) {
                $bind[] = &$value;
            }
            call_user_func_array([$stmt, 'bind_param'], $bind);
        }
        if (!$stmt->execute()) {
            throw new RuntimeException('SQL execute failed: ' . $stmt->error);
        }
        return $stmt;
    }

    private function rows(string $sql, string $types = '', array $params = []): array {
        $stmt = $this->stmt($sql, $types, $params);
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function row(string $sql, string $types = '', array $params = []): ?array {
        $rows = $this->rows($sql, $types, $params);
        return $rows[0] ?? null;
    }

    private function scalar(string $sql, string $types = '', array $params = []) {
        $row = $this->row($sql, $types, $params);
        if (!$row) return 0;
        return array_values($row)[0] ?? 0;
    }

    private function exec(string $sql, string $types = '', array $params = []): bool {
        $this->stmt($sql, $types, $params);
        return true;
    }

    public function authenticateAdmin(string $email, string $password): ?array {
        $user = $this->row(
            "SELECT id, name, email, password_hash, role, is_active, is_verified
             FROM users
             WHERE email = ? AND role = 'admin'
             LIMIT 1",
            's', [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        if ((int)$user['is_active'] !== 1) {
            return ['error' => 'inactive'];
        }
        return $user;
    }

    public function dashboardStats(): array {
        $roleCounts = ['seeker' => 0, 'employer' => 0, 'recruiter' => 0, 'admin' => 0];
        foreach ($this->rows("SELECT role, COUNT(*) AS total FROM users GROUP BY role") as $row) {
            $roleCounts[$row['role']] = (int)$row['total'];
        }

        return [
            'role_counts' => $roleCounts,
            'total_users' => array_sum($roleCounts),
            'active_jobs' => (int)$this->scalar("SELECT COUNT(*) FROM jobs WHERE status = 'active'"),
            'applications_today' => (int)$this->scalar("SELECT COUNT(*) FROM applications WHERE DATE(applied_at) = CURDATE()"),
            'pending_verifications' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE role IN ('employer','recruiter') AND is_verified = 0 AND is_active = 1"),
            'open_complaints' => (int)$this->scalar("SELECT COUNT(*) FROM complaints WHERE status = 'open'"),
            'featured_jobs' => (int)$this->scalar("SELECT COUNT(*) FROM jobs WHERE is_featured = 1"),
            'total_applications' => (int)$this->scalar("SELECT COUNT(*) FROM applications"),
        ];
    }

    public function recentComplaints(int $limit = 5): array {
        return $this->rows(
            "SELECT c.*, u.name AS submitter_name, u.role AS submitter_role, su.name AS subject_name
             FROM complaints c
             JOIN users u ON u.id = c.submitter_id
             LEFT JOIN users su ON su.id = c.subject_id
             ORDER BY c.created_at DESC
             LIMIT ?",
            'i', [$limit]
        );
    }

    public function pendingAccounts(int $limit = 8): array {
        return $this->rows(
            "SELECT u.id, u.name, u.email, u.phone, u.role, u.is_active, u.is_verified, u.created_at,
                    ep.company_name, rp.agency_name
             FROM users u
             LEFT JOIN employer_profiles ep ON ep.user_id = u.id
             LEFT JOIN recruiter_profiles rp ON rp.user_id = u.id
             WHERE u.role IN ('employer','recruiter') AND u.is_verified = 0 AND u.is_active = 1
             ORDER BY u.created_at DESC
             LIMIT ?",
            'i', [$limit]
        );
    }

    public function listAccounts(string $role, string $status = 'all', string $search = ''): array {
        $allowedRoles = ['employer', 'recruiter', 'seeker', 'admin'];
        if (!in_array($role, $allowedRoles, true)) {
            return [];
        }

        $sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.profile_pic, u.is_active, u.is_verified, u.created_at,
                       ep.company_name, ep.industry, ep.company_size,
                       rp.agency_name, rp.specialization,
                       sp.headline, sp.skills, sp.years_experience, sp.preferred_location
                FROM users u
                LEFT JOIN employer_profiles ep ON ep.user_id = u.id
                LEFT JOIN recruiter_profiles rp ON rp.user_id = u.id
                LEFT JOIN seeker_profiles sp ON sp.user_id = u.id
                WHERE u.role = ?";
        $types = 's';
        $params = [$role];

        if ($status === 'pending') {
            $sql .= " AND u.is_verified = 0 AND u.is_active = 1";
        } elseif ($status === 'verified') {
            $sql .= " AND u.is_verified = 1 AND u.is_active = 1";
        } elseif ($status === 'suspended') {
            $sql .= " AND u.is_active = 0";
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR ep.company_name LIKE ? OR rp.agency_name LIKE ? OR sp.headline LIKE ? OR sp.skills LIKE ?)";
            $types .= 'sssssss';
            array_push($params, $like, $like, $like, $like, $like, $like, $like);
        }

        $sql .= " ORDER BY u.created_at DESC, u.id DESC";
        return $this->rows($sql, $types, $params);
    }

    public function getUserDetails(int $userId): ?array {
        return $this->row(
            "SELECT u.*, ep.company_name, ep.industry, ep.company_size, ep.description AS company_description, ep.website AS company_website, ep.address, ep.logo_path,
                    rp.agency_name, rp.specialization, rp.description AS recruiter_description, rp.website AS recruiter_website,
                    sp.headline, sp.summary, sp.skills, sp.years_experience, sp.education_level, sp.current_salary, sp.expected_salary, sp.preferred_location, sp.resume_path
             FROM users u
             LEFT JOIN employer_profiles ep ON ep.user_id = u.id
             LEFT JOIN recruiter_profiles rp ON rp.user_id = u.id
             LEFT JOIN seeker_profiles sp ON sp.user_id = u.id
             WHERE u.id = ?
             LIMIT 1",
            'i', [$userId]
        );
    }

    public function updateUserStatus(int $adminId, int $targetUserId, string $action, string $reason = ''): array {
        $target = $this->row("SELECT id, role, name, is_active, is_verified FROM users WHERE id = ? LIMIT 1", 'i', [$targetUserId]);
        if (!$target) {
            return ['ok' => false, 'message' => 'User not found.'];
        }
        if ($target['role'] === 'admin' && $targetUserId === $adminId) {
            return ['ok' => false, 'message' => 'You cannot change your own admin account status.'];
        }

        $message = '';
        if ($action === 'approve') {
            $this->exec("UPDATE users SET is_verified = 1, is_active = 1 WHERE id = ?", 'i', [$targetUserId]);
            $message = 'Account approved successfully.';
        } elseif ($action === 'reject') {
            if (trim($reason) === '') {
                return ['ok' => false, 'message' => 'Rejection reason is required.'];
            }
            $this->exec("UPDATE users SET is_verified = 0, is_active = 0 WHERE id = ?", 'i', [$targetUserId]);
            $message = 'Account rejected and deactivated.';
        } elseif ($action === 'suspend') {
            if (trim($reason) === '') {
                return ['ok' => false, 'message' => 'Suspension reason is required.'];
            }
            $this->exec("UPDATE users SET is_active = 0 WHERE id = ?", 'i', [$targetUserId]);
            $message = 'Account suspended.';
        } elseif ($action === 'reactivate') {
            $this->exec("UPDATE users SET is_active = 1 WHERE id = ?", 'i', [$targetUserId]);
            $message = 'Account reactivated.';
        } else {
            return ['ok' => false, 'message' => 'Invalid action.'];
        }

        $this->logAdminAction($adminId, $targetUserId, 'user', $action, $reason);
        return ['ok' => true, 'message' => $message];
    }

    public function logAdminAction(int $adminId, ?int $targetId, string $targetType, string $action, string $reason = ''): void {
        $this->exec(
            "INSERT INTO admin_action_logs (admin_id, target_id, target_type, action, reason, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            'iisss', [$adminId, $targetId, $targetType, $action, $reason]
        );
    }

    public function categoriesWithCounts(): array {
        return $this->rows(
            "SELECT c.id, c.name, c.description,
                    COUNT(j.id) AS total_jobs,
                    SUM(CASE WHEN j.status = 'active' THEN 1 ELSE 0 END) AS active_jobs
             FROM categories c
             LEFT JOIN jobs j ON j.category_id = c.id
             GROUP BY c.id, c.name, c.description
             ORDER BY c.name ASC"
        );
    }

    public function createCategory(string $name, string $description): array {
        if ($name === '') {
            return ['ok' => false, 'message' => 'Category name is required.'];
        }
        $exists = $this->scalar("SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(?)", 's', [$name]);
        if ((int)$exists > 0) {
            return ['ok' => false, 'message' => 'This category already exists.'];
        }
        $this->exec("INSERT INTO categories (name, description) VALUES (?, ?)", 'ss', [$name, $description]);
        return ['ok' => true, 'message' => 'Category added successfully.'];
    }

    public function renameCategory(int $id, string $name, string $description): array {
        if ($id <= 0 || $name === '') {
            return ['ok' => false, 'message' => 'Valid category name is required.'];
        }
        $exists = $this->scalar("SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(?) AND id <> ?", 'si', [$name, $id]);
        if ((int)$exists > 0) {
            return ['ok' => false, 'message' => 'Another category already uses this name.'];
        }
        $this->exec("UPDATE categories SET name = ?, description = ? WHERE id = ?", 'ssi', [$name, $description, $id]);
        return ['ok' => true, 'message' => 'Category updated successfully.'];
    }

    public function deleteCategory(int $id): array {
        $jobCount = (int)$this->scalar("SELECT COUNT(*) FROM jobs WHERE category_id = ?", 'i', [$id]);
        if ($jobCount > 0) {
            return ['ok' => false, 'message' => 'Category cannot be deleted because jobs still reference it.'];
        }
        $this->exec("DELETE FROM categories WHERE id = ?", 'i', [$id]);
        return ['ok' => true, 'message' => 'Category deleted successfully.'];
    }

    public function listJobs(array $filters = []): array {
        $sql = "SELECT j.*, c.name AS category_name,
                       eu.name AS employer_user_name, ep.company_name,
                       ru.name AS recruiter_user_name, rp.agency_name,
                       COUNT(a.id) AS application_count
                FROM jobs j
                JOIN categories c ON c.id = j.category_id
                JOIN users eu ON eu.id = j.employer_id
                LEFT JOIN employer_profiles ep ON ep.user_id = j.employer_id
                LEFT JOIN users ru ON ru.id = j.recruiter_id
                LEFT JOIN recruiter_profiles rp ON rp.user_id = j.recruiter_id
                LEFT JOIN applications a ON a.job_id = j.id
                WHERE 1 = 1";
        $types = '';
        $params = [];

        if (!empty($filters['status']) && in_array($filters['status'], ['active','closed','draft'], true)) {
            $sql .= " AND j.status = ?";
            $types .= 's';
            $params[] = $filters['status'];
        }
        if (!empty($filters['employer_id'])) {
            $sql .= " AND j.employer_id = ?";
            $types .= 'i';
            $params[] = (int)$filters['employer_id'];
        }
        if (!empty($filters['recruiter_id'])) {
            $sql .= " AND j.recruiter_id = ?";
            $types .= 'i';
            $params[] = (int)$filters['recruiter_id'];
        }
        if (!empty($filters['featured'])) {
            $sql .= " AND j.is_featured = 1";
        }
        if (!empty($filters['search'])) {
            $like = '%' . $filters['search'] . '%';
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR ep.company_name LIKE ? OR rp.agency_name LIKE ? OR j.location LIKE ?)";
            $types .= 'sssss';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $sql .= " GROUP BY j.id
                  ORDER BY j.is_featured DESC, j.created_at DESC, j.id DESC";
        return $this->rows($sql, $types, $params);
    }

    public function jobFilterOptions(): array {
        return [
            'employers' => $this->rows("SELECT id, name, email FROM users WHERE role = 'employer' ORDER BY name ASC"),
            'recruiters' => $this->rows("SELECT id, name, email FROM users WHERE role = 'recruiter' ORDER BY name ASC"),
        ];
    }

    public function updateJobStatus(int $jobId, string $status): array {
        if (!in_array($status, ['active','closed','draft'], true)) {
            return ['ok' => false, 'message' => 'Invalid job status.'];
        }
        $this->exec("UPDATE jobs SET status = ? WHERE id = ?", 'si', [$status, $jobId]);
        return ['ok' => true, 'message' => 'Job status updated.'];
    }

    public function deleteJob(int $jobId): array {
        $this->exec("DELETE FROM jobs WHERE id = ?", 'i', [$jobId]);
        return ['ok' => true, 'message' => 'Job removed from the platform.'];
    }

    public function toggleFeatured(int $jobId): array {
        $job = $this->row("SELECT id, is_featured FROM jobs WHERE id = ? LIMIT 1", 'i', [$jobId]);
        if (!$job) {
            return ['ok' => false, 'message' => 'Job not found.'];
        }
        $newValue = ((int)$job['is_featured'] === 1) ? 0 : 1;
        $this->exec("UPDATE jobs SET is_featured = ? WHERE id = ?", 'ii', [$newValue, $jobId]);
        return ['ok' => true, 'message' => $newValue ? 'Job marked as featured.' : 'Featured status removed.', 'is_featured' => $newValue];
    }

    public function listComplaints(string $status = 'all'): array {
        $sql = "SELECT c.*, u.name AS submitter_name, u.email AS submitter_email, u.role AS submitter_role,
                       su.name AS subject_name, su.email AS subject_email, su.role AS subject_role
                FROM complaints c
                JOIN users u ON u.id = c.submitter_id
                LEFT JOIN users su ON su.id = c.subject_id
                WHERE 1 = 1";
        $types = '';
        $params = [];
        if (in_array($status, ['open','resolved'], true)) {
            $sql .= " AND c.status = ?";
            $types .= 's';
            $params[] = $status;
        }
        $sql .= " ORDER BY CASE WHEN c.status = 'open' THEN 0 ELSE 1 END, c.created_at DESC";
        return $this->rows($sql, $types, $params);
    }

    public function resolveComplaint(int $complaintId, string $note): array {
        if (trim($note) === '') {
            return ['ok' => false, 'message' => 'Admin resolution note is required.'];
        }
        $this->exec("UPDATE complaints SET status = 'resolved', admin_note = ? WHERE id = ?", 'si', [$note, $complaintId]);
        return ['ok' => true, 'message' => 'Complaint marked as resolved.'];
    }

    public function policies(): array {
        $policies = [
            'max_jobs_per_employer' => '10',
            'max_active_applications_per_seeker' => '20',
            'resume_visibility_default' => 'private',
        ];
        foreach ($this->rows("SELECT policy_key, policy_value FROM platform_policies") as $row) {
            $policies[$row['policy_key']] = $row['policy_value'];
        }
        return $policies;
    }

    public function savePolicies(array $input): array {
        $maxJobs = max(1, (int)($input['max_jobs_per_employer'] ?? 10));
        $maxApps = max(1, (int)($input['max_active_applications_per_seeker'] ?? 20));
        $resume = ($input['resume_visibility_default'] ?? 'private') === 'public' ? 'public' : 'private';

        $items = [
            'max_jobs_per_employer' => (string)$maxJobs,
            'max_active_applications_per_seeker' => (string)$maxApps,
            'resume_visibility_default' => $resume,
        ];

        foreach ($items as $key => $value) {
            $this->exec(
                "INSERT INTO platform_policies (policy_key, policy_value, updated_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE policy_value = VALUES(policy_value), updated_at = NOW()",
                'ss', [$key, $value]
            );
        }
        return ['ok' => true, 'message' => 'Platform policies saved.'];
    }

    public function createAnnouncement(int $adminId, string $title, string $body, bool $isActive): array {
        if (trim($title) === '' || trim($body) === '') {
            return ['ok' => false, 'message' => 'Announcement title and message are required.'];
        }
        $active = $isActive ? 1 : 0;
        $this->exec(
            "INSERT INTO announcements (admin_id, title, body, is_active, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            'issi', [$adminId, $title, $body, $active]
        );
        return ['ok' => true, 'message' => 'Announcement posted successfully.'];
    }

    public function listAnnouncements(): array {
        return $this->rows(
            "SELECT a.*, u.name AS admin_name
             FROM announcements a
             JOIN users u ON u.id = a.admin_id
             ORDER BY a.created_at DESC"
        );
    }

    public function toggleAnnouncement(int $id): array {
        $row = $this->row("SELECT id, is_active FROM announcements WHERE id = ? LIMIT 1", 'i', [$id]);
        if (!$row) {
            return ['ok' => false, 'message' => 'Announcement not found.'];
        }
        $newValue = ((int)$row['is_active'] === 1) ? 0 : 1;
        $this->exec("UPDATE announcements SET is_active = ? WHERE id = ?", 'ii', [$newValue, $id]);
        return ['ok' => true, 'message' => 'Announcement status updated.'];
    }

    public function platformAnalytics(): array {
        return [
            'jobs_by_category' => $this->rows(
                "SELECT c.name, COUNT(j.id) AS total
                 FROM categories c
                 LEFT JOIN jobs j ON j.category_id = c.id
                 GROUP BY c.id, c.name
                 ORDER BY total DESC, c.name ASC"
            ),
            'application_volume' => $this->rows(
                "SELECT DATE(applied_at) AS day, COUNT(*) AS total
                 FROM applications
                 GROUP BY DATE(applied_at)
                 ORDER BY day DESC
                 LIMIT 30"
            ),
            'top_employers' => $this->rows(
                "SELECT COALESCE(ep.company_name, u.name) AS employer_name, COUNT(a.id) AS total_applications
                 FROM users u
                 LEFT JOIN employer_profiles ep ON ep.user_id = u.id
                 LEFT JOIN jobs j ON j.employer_id = u.id
                 LEFT JOIN applications a ON a.job_id = j.id
                 WHERE u.role = 'employer'
                 GROUP BY u.id, employer_name
                 ORDER BY total_applications DESC
                 LIMIT 10"
            ),
            'active_recruiters' => $this->rows(
                "SELECT COALESCE(rp.agency_name, u.name) AS recruiter_name, COUNT(j.id) AS posted_jobs
                 FROM users u
                 LEFT JOIN recruiter_profiles rp ON rp.user_id = u.id
                 LEFT JOIN jobs j ON j.recruiter_id = u.id
                 WHERE u.role = 'recruiter'
                 GROUP BY u.id, recruiter_name
                 ORDER BY posted_jobs DESC
                 LIMIT 10"
            ),
            'popular_locations' => $this->rows(
                "SELECT location, COUNT(*) AS total
                 FROM jobs
                 WHERE location IS NOT NULL AND location <> ''
                 GROUP BY location
                 ORDER BY total DESC
                 LIMIT 10"
            ),
            'popular_job_types' => $this->rows(
                "SELECT job_type, COUNT(*) AS total
                 FROM jobs
                 GROUP BY job_type
                 ORDER BY total DESC"
            ),
        ];
    }

    public function userGrowthReport(): array {
        return $this->rows(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, role, COUNT(*) AS total
             FROM users
             GROUP BY DATE_FORMAT(created_at, '%Y-%m'), role
             ORDER BY month DESC, role ASC"
        );
    }

    public function monthlySummary(string $month): array {
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        $start = $month . '-01';
        $end = date('Y-m-d', strtotime($start . ' +1 month'));

        return [
            'month' => $month,
            'new_users' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'new_seekers' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE role = 'seeker' AND created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'new_employers' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE role = 'employer' AND created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'new_recruiters' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE role = 'recruiter' AND created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'jobs_posted' => (int)$this->scalar("SELECT COUNT(*) FROM jobs WHERE created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'active_jobs' => (int)$this->scalar("SELECT COUNT(*) FROM jobs WHERE status = 'active' AND created_at < ?", 's', [$end]),
            'applications' => (int)$this->scalar("SELECT COUNT(*) FROM applications WHERE applied_at >= ? AND applied_at < ?", 'ss', [$start, $end]),
            'complaints_opened' => (int)$this->scalar("SELECT COUNT(*) FROM complaints WHERE created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
            'complaints_resolved' => (int)$this->scalar("SELECT COUNT(*) FROM complaints WHERE status = 'resolved' AND created_at >= ? AND created_at < ?", 'ss', [$start, $end]),
        ];
    }

    public function adminActionLogs(int $limit = 25): array {
        return $this->rows(
            "SELECT l.*, au.name AS admin_name, tu.name AS target_name
             FROM admin_action_logs l
             JOIN users au ON au.id = l.admin_id
             LEFT JOIN users tu ON tu.id = l.target_id
             ORDER BY l.created_at DESC
             LIMIT ?",
            'i', [$limit]
        );
    }
}
?>
