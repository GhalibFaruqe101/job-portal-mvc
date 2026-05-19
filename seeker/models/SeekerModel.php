<?php

require_once __DIR__ . '/../config/db.php';

class SeekerModel {

    // ── Auth ──────────────────────────────────────────────────────────────────
    public function registerUser(string $name, string $email, string $phone, string $hash): int|false {
        $db = getDB();
        $s = $db->prepare("INSERT INTO users (name,email,password_hash,phone,role,is_active,is_verified,created_at) VALUES (?,?,?,?,'seeker',1,0,NOW())");
        $s->bind_param('ssss', $name, $email, $hash, $phone);
        return $s->execute() ? $db->insert_id : false;
    }
    public function getUserByEmail(string $email): array|null {
        $db = getDB();
        $s = $db->prepare("SELECT * FROM users WHERE email=? AND role='seeker' LIMIT 1");
        $s->bind_param('s', $email); $s->execute();
        return $s->get_result()->fetch_assoc() ?: null;
    }
    public function getUserById(int $id): array|null {
        $db = getDB();
        $s = $db->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
        $s->bind_param('i', $id); $s->execute();
        return $s->get_result()->fetch_assoc() ?: null;
    }
    public function emailExists(string $email): bool {
        $db = getDB();
        $s = $db->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $s->bind_param('s', $email); $s->execute(); $s->store_result();
        return $s->num_rows > 0;
    }

    // ── Seeker Profile ────────────────────────────────────────────────────────
    public function getSeekerProfile(int $uid): array|null {
        $db = getDB();
        $s = $db->prepare("SELECT * FROM seeker_profiles WHERE user_id=? LIMIT 1");
        $s->bind_param('i', $uid); $s->execute();
        return $s->get_result()->fetch_assoc() ?: null;
    }
    public function upsertSeekerProfile(
        int $uid, string $headline, string $summary, string $skills,
        int $yrs, string $edu, float $curr, float $exp, string $loc, string $resume=''
    ): bool {
        $db = getDB();
        $s = $db->prepare(
            "INSERT INTO seeker_profiles
               (user_id,headline,summary,skills,years_experience,education_level,
                current_salary,expected_salary,preferred_location,resume_path)
             VALUES (?,?,?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE
               headline=VALUES(headline), summary=VALUES(summary),
               skills=VALUES(skills), years_experience=VALUES(years_experience),
               education_level=VALUES(education_level),
               current_salary=VALUES(current_salary),
               expected_salary=VALUES(expected_salary),
               preferred_location=VALUES(preferred_location),
               resume_path=IF(VALUES(resume_path)!='',VALUES(resume_path),resume_path)"
        );
        $s->bind_param('isssissdss', $uid, $headline, $summary, $skills, $yrs, $edu, $curr, $exp, $loc, $resume);
        return $s->execute();
    }
    public function updateProfilePic(int $uid, string $path): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $s->bind_param('si', $path, $uid); return $s->execute();
    }
    public function updateResumePath(int $uid, string $path): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE seeker_profiles SET resume_path=? WHERE user_id=?");
        $s->bind_param('si', $path, $uid); return $s->execute();
    }

    // ── Job Search ────────────────────────────────────────────────────────────
    private const PER_PAGE = 10;
    public function searchJobs(
        string $kw='', int $catId=0, string $loc='', string $type='',
        string $lvl='', float $salMin=0, float $salMax=0, int $page=1
    ): array {
        $db     = getDB();
        $offset = ($page - 1) * self::PER_PAGE;
        $limit  = self::PER_PAGE;

        $base = "FROM jobs j
                 LEFT JOIN categories c       ON j.category_id = c.id
                 LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
                 LEFT JOIN users u             ON j.employer_id = u.id
                 LEFT JOIN recruiter_profiles rp ON j.recruiter_id = rp.user_id
                 WHERE j.status='active'";

        $params = []; $types = '';

        if ($kw !== '') {
            $w = '%'.$kw.'%';
            $base .= " AND (j.title LIKE ? OR j.description LIKE ? OR ep.company_name LIKE ?)";
            $params[] = $w; $params[] = $w; $params[] = $w; $types .= 'sss';
        }
        if ($catId > 0)  { $base .= " AND j.category_id=?";      $params[]=$catId;  $types.='i'; }
        if ($loc !== '')  { $l='%'.$loc.'%'; $base.=" AND j.location LIKE ?"; $params[]=$l; $types.='s'; }
        if ($type !== '') { $base .= " AND j.job_type=?";          $params[]=$type;  $types.='s'; }
        if ($lvl !== '')  { $base .= " AND j.experience_level=?";  $params[]=$lvl;   $types.='s'; }
        if ($salMin > 0)  { $base .= " AND j.salary_max>=?";       $params[]=$salMin;$types.='d'; }
        if ($salMax > 0)  { $base .= " AND j.salary_min<=?";       $params[]=$salMax;$types.='d'; }

        $countSql = "SELECT COUNT(*) ".$base;
        $cs = $db->prepare($countSql);
        if ($params) $cs->bind_param($types, ...$params);
        $cs->execute(); $cs->bind_result($total); $cs->fetch(); $cs->close();

        $dataSql = "SELECT j.*,c.name AS category_name,
                    ep.company_name,ep.logo_path,ep.description AS company_desc,
                    ep.website,ep.address,ep.industry,u.name AS poster_name,rp.agency_name
                    ".$base." ORDER BY j.is_featured DESC, j.created_at DESC
                    LIMIT ? OFFSET ?";
        $ds = $db->prepare($dataSql);
        $allParams = $params; $allParams[]=$limit; $allParams[]=$offset;
        $allTypes  = $types.'ii';
        if ($allParams) $ds->bind_param($allTypes, ...$allParams);
        $ds->execute();
        $rows = $ds->get_result()->fetch_all(MYSQLI_ASSOC);

        return [$rows, (int)$total];
    }
    public function getJobById(int $id): array|null {
        $db = getDB();
        $s = $db->prepare(
            "SELECT j.*,c.name AS category_name,
             ep.company_name,ep.logo_path,ep.description AS company_desc,
             ep.website,ep.address,ep.industry,u.name AS poster_name,rp.agency_name
             FROM jobs j
             LEFT JOIN categories c ON j.category_id=c.id
             LEFT JOIN employer_profiles ep ON j.employer_id=ep.user_id
             LEFT JOIN users u ON j.employer_id=u.id
             LEFT JOIN recruiter_profiles rp ON j.recruiter_id=rp.user_id
             WHERE j.id=? AND j.status='active' LIMIT 1"
        );
        $s->bind_param('i',$id); $s->execute();
        return $s->get_result()->fetch_assoc() ?: null;
    }
    public function getCategories(): array {
        return getDB()->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
    }

    // ── Applications ──────────────────────────────────────────────────────────
    public function hasActiveApplication(int $seekerId, int $jobId): bool {
        $db = getDB();
        $s = $db->prepare("SELECT id FROM applications WHERE job_id=? AND seeker_id=? AND status != 'withdrawn' LIMIT 1");
        $s->bind_param('ii',$jobId,$seekerId); $s->execute(); $s->store_result();
        return $s->num_rows > 0;
    }
    public function applyToJob(int $uid, int $jid, string $cv, string $resume): int|false {
        $db = getDB();
        $s = $db->prepare("INSERT INTO applications (job_id,seeker_id,cover_letter,resume_path,status,applied_at) VALUES (?,?,?,?,'submitted',NOW())");
        $s->bind_param('iiss',$jid,$uid,$cv,$resume);
        return $s->execute() ? $db->insert_id : false;
    }
    public function getApplicationsBySeeker(int $uid): array {
        $db = getDB();
        $s = $db->prepare(
            "SELECT a.*,j.title AS job_title,j.location,ep.company_name,ep.logo_path
             FROM applications a
             JOIN jobs j ON a.job_id=j.id
             LEFT JOIN employer_profiles ep ON j.employer_id=ep.user_id
             WHERE a.seeker_id=? ORDER BY a.applied_at DESC"
        );
        $s->bind_param('i',$uid); $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function withdrawApplication(int $appId, int $uid): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE applications SET status='withdrawn' WHERE id=? AND seeker_id=? AND status='submitted'");
        $s->bind_param('ii',$appId,$uid); $s->execute();
        return $s->affected_rows > 0;
    }

    // ── Saved Jobs ────────────────────────────────────────────────────────────
    public function getSavedJobs(int $uid): array {
        $db = getDB();
        $s = $db->prepare(
            "SELECT sj.*,j.title,j.location,j.job_type,j.salary_min,j.salary_max,
             j.deadline,j.status AS job_status,ep.company_name,ep.logo_path
             FROM saved_jobs sj JOIN jobs j ON sj.job_id=j.id
             LEFT JOIN employer_profiles ep ON j.employer_id=ep.user_id
             WHERE sj.user_id=? ORDER BY sj.saved_at DESC"
        );
        $s->bind_param('i',$uid); $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function isJobSaved(int $uid, int $jid): bool {
        $db = getDB();
        $s = $db->prepare("SELECT id FROM saved_jobs WHERE user_id=? AND job_id=? LIMIT 1");
        $s->bind_param('ii',$uid,$jid); $s->execute(); $s->store_result();
        return $s->num_rows > 0;
    }
    public function saveJob(int $uid, int $jid): bool {
        $db = getDB();
        $s = $db->prepare("INSERT IGNORE INTO saved_jobs (user_id,job_id,saved_at) VALUES (?,?,NOW())");
        $s->bind_param('ii',$uid,$jid); return $s->execute();
    }
    public function unsaveJob(int $uid, int $jid): bool {
        $db = getDB();
        $s = $db->prepare("DELETE FROM saved_jobs WHERE user_id=? AND job_id=?");
        $s->bind_param('ii',$uid,$jid); return $s->execute();
    }

    // ── Alerts ────────────────────────────────────────────────────────────────
    public function getAlertsBySeeker(int $uid): array {
        $db = getDB();
        $s = $db->prepare(
            "SELECT ja.*,c.name AS category_name
             FROM job_alerts ja LEFT JOIN categories c ON ja.category_id=c.id
             WHERE ja.seeker_id=? ORDER BY ja.created_at DESC"
        );
        $s->bind_param('i',$uid); $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function createAlert(int $uid, string $kw, int $catId=0, string $loc='', string $type=''): bool {
        $db = getDB();
        $cid = $catId > 0 ? $catId : null;
        $s = $db->prepare("INSERT INTO job_alerts (seeker_id,keyword,category_id,location,job_type,created_at) VALUES (?,?,?,?,?,NOW())");
        $s->bind_param('isiss',$uid,$kw,$cid,$loc,$type); return $s->execute();
    }
    public function deleteAlert(int $aid, int $uid): bool {
        $db = getDB();
        $s = $db->prepare("DELETE FROM job_alerts WHERE id=? AND seeker_id=?");
        $s->bind_param('ii',$aid,$uid); return $s->execute();
    }

    // ── Alert matching ────────────────────────────────────────────────────────
    public function getMatchedAlertJobs(int $seekerId): array {
        $db     = getDB();
        $alerts = $this->getAlertsBySeeker($seekerId);
        if (empty($alerts)) return [];

        $unionParts = []; $params = []; $types = '';
        foreach ($alerts as $alert) {
            $kw = '%' . ($alert['keyword'] ?? '') . '%';
            $part = "(SELECT j.id,j.title,j.location,j.job_type,j.deadline,ep.company_name,ep.logo_path
                      FROM jobs j LEFT JOIN employer_profiles ep ON j.employer_id=ep.user_id
                      WHERE j.status='active' AND (j.title LIKE ? OR j.description LIKE ? OR ep.company_name LIKE ?)";
            $params[] = $kw; $params[] = $kw; $params[] = $kw; $types .= 'sss';

            if (!empty($alert['category_id'])) { $part .= " AND j.category_id=?"; $params[] = (int)$alert['category_id']; $types .= 'i'; }
            if (!empty($alert['location']))    { $l = '%'.$alert['location'].'%'; $part .= " AND j.location LIKE ?"; $params[] = $l; $types .= 's'; }
            if (!empty($alert['job_type']))    { $part .= " AND j.job_type=?"; $params[] = $alert['job_type']; $types .= 's'; }
            $part .= ")";
            $unionParts[] = $part;
        }

        $sql = implode(' UNION ', $unionParts) . " ORDER BY deadline ASC LIMIT 20";
        $s   = $db->prepare($sql);
        if ($params) $s->bind_param($types, ...$params);
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ── Messages ──────────────────────────────────────────────────────────────
    public function getInboxMessages(int $uid): array {
        $db = getDB();
        $s = $db->prepare(
            "SELECT m.*, 
                    u_sender.name AS sender_name, u_sender.profile_pic AS sender_pic,
                    u_recipient.name AS recipient_name,
                    j.title AS job_title
             FROM messages m 
             JOIN users u_sender ON m.sender_id = u_sender.id
             JOIN users u_recipient ON m.recipient_id = u_recipient.id
             LEFT JOIN applications a ON m.application_id = a.id
             LEFT JOIN jobs j ON a.job_id = j.id
             WHERE m.recipient_id = ? OR m.sender_id = ? 
             ORDER BY m.sent_at DESC"
        );
        $s->bind_param('ii', $uid, $uid); 
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function sendMessage(int $from, int $to, int $appId, string $body): bool {
        $db = getDB();
        $s = $db->prepare("INSERT INTO messages (sender_id,recipient_id,application_id,body,sent_at,is_read) VALUES (?,?,?,?,NOW(),0)");
        $s->bind_param('iiis',$from,$to,$appId,$body); return $s->execute();
    }
    public function markMessageRead(int $msgId, int $uid): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE messages SET is_read=1 WHERE id=? AND recipient_id=?");
        $s->bind_param('ii',$msgId,$uid); return $s->execute();
    }
    public function markAllMessagesRead(int $uid): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE messages SET is_read=1 WHERE recipient_id=? AND is_read=0");
        $s->bind_param('i',$uid); return $s->execute();
    }
    public function verifyApplicationOwnership(int $appId, int $seekerId, int $recipientId): bool {
        $db = getDB();
        $s = $db->prepare("SELECT a.id FROM applications a JOIN jobs j ON a.job_id=j.id WHERE a.id=? AND a.seeker_id=? AND j.employer_id=? LIMIT 1");
        $s->bind_param('iii',$appId,$seekerId,$recipientId); $s->execute(); $s->store_result();
        return $s->num_rows > 0;
    }

    // ── Recruiter Outreach ────────────────────────────────────────────────────
    public function getRecruiterOutreach(int $uid): array {
        $db = getDB();
        $s = $db->prepare(
            "SELECT ro.*, u.name AS recruiter_name, u.profile_pic,
                    rp.agency_name, j.title AS job_title
             FROM recruiter_outreach ro 
             LEFT JOIN users u ON ro.recruiter_id = u.id
             LEFT JOIN recruiter_profiles rp ON ro.recruiter_id = rp.user_id
             LEFT JOIN jobs j ON ro.job_id = j.id
             WHERE ro.seeker_id = ? 
             ORDER BY ro.sent_at DESC"
        );
        $s->bind_param('i', $uid); $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function updateOutreachStatus(int $oid, int $uid, string $status): bool {
        $db = getDB();
        $s = $db->prepare("UPDATE recruiter_outreach SET status=? WHERE id=? AND seeker_id=?");
        $s->bind_param('sii',$status,$oid,$uid); return $s->execute();
    }

    // ── Complaints ────────────────────────────────────────────────────────────
    public function submitComplaint(int $from, int $subject, string $desc): bool {
        $db = getDB();
        $s = $db->prepare("INSERT INTO complaints (submitter_id,subject_id,description,status,created_at) VALUES (?,?,?,'open',NOW())");
        $s->bind_param('iis',$from,$subject,$desc); return $s->execute();
    }
}