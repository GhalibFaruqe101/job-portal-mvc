<?php

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/SeekerModel.php';

class SeekerController {

    private SeekerModel $model;
    private const UPLOAD_DIR_RESUME = __DIR__ . '/../assets/uploads/resumes/';
    private const UPLOAD_DIR_PICS   = __DIR__ . '/../assets/uploads/profile_pics/';
    private const MAX_RESUME_SIZE   = 5 * 1024 * 1024;
    private const ALLOWED_PIC_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct() { $this->model = new SeekerModel(); }

    public function dispatch(string $action): void {
        $public = ['login','register','doLogin','doRegister'];
        if (!in_array($action, $public)) $this->requireAuth();
        match($action) {
            'register'        => $this->showRegister(),
            'doRegister'      => $this->doRegister(),
            'login'           => $this->showLogin(),
            'doLogin'         => $this->doLogin(),
            'logout'          => $this->doLogout(),
            'dashboard'       => $this->showDashboard(),
            'profile'         => $this->showProfile(),
            'editProfile'     => $this->showEditProfile(),
            'saveProfile'     => $this->saveProfile(),
            'uploadResume'    => $this->uploadResume(),
            'uploadPic'       => $this->uploadPic(),
            'jobs'            => $this->showJobs(),
            'jobDetail'       => $this->showJobDetail(),
            'applyJob'        => $this->applyJob(),
            'withdraw'        => $this->withdrawApplication(),
            'applications'    => $this->showApplications(),
            'saveJob'         => $this->toggleSaveJob(),
            'savedJobs'       => $this->showSavedJobs(),
            'alerts'          => $this->showAlerts(),
            'createAlert'     => $this->createAlert(),
            'deleteAlert'     => $this->deleteAlert(),
            'messages'        => $this->showMessages(),
            'sendMessage'     => $this->sendMessage(),
            'outreach'        => $this->showOutreach(),
            'respondOutreach' => $this->respondOutreach(),
            'complaint'       => $this->showComplaintForm(),
            'submitComplaint' => $this->submitComplaint(),
            default           => $this->notFound(),
        };
    }

    // ── Auth ─────────────────────────────────────────────────────────────────
    private function requireAuth(): void {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
            header('Location: index.php?action=login'); exit;
        }
    }
    private function userId(): int { return (int)$_SESSION['user_id']; }

    // ── CSRF ─────────────────────────────────────────────────────────────────
    private function csrfToken(): string {
        if (empty($_SESSION['csrf_secret']))
            $_SESSION['csrf_secret'] = bin2hex(random_bytes(32));
        return hash_hmac('sha256', session_id(), $_SESSION['csrf_secret']);
    }
    private function verifyCsrf(): void {
        $token = trim($_POST['csrf_token'] ?? '');
        if (!$token || !hash_equals($this->csrfToken(), $token)) {
            http_response_code(403);
            die('<h1>403 — Invalid or missing CSRF token. Please go back and try again.</h1>');
        }
    }

    // ── Register ──────────────────────────────────────────────────────────────
    private function showRegister(): void {
        $this->render('seeker/register', ['error' => null, 'csrf' => $this->csrfToken()]);
    }
    private function doRegister(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showRegister(); return; }
        $this->verifyCsrf();
        $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? ''); $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_pass'] ?? '';
        $fail = fn($m) => $this->render('seeker/register', ['error'=>$m,'csrf'=>$this->csrfToken()]);
        if (!$name||!$email||!$phone||!$pass) { $fail('All fields are required.'); return; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $fail('Invalid email address.'); return; }
        if (strlen($pass) < 6) { $fail('Password must be at least 6 characters.'); return; }
        if ($pass !== $confirm) { $fail('Passwords do not match.'); return; }
        if ($this->model->emailExists($email)) { $fail('Email already registered.'); return; }
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $userId = $this->model->registerUser($name, $email, $phone, $hash);
        if ($userId) {
            // Auto-login after registration (is_verified=0; add email flow later if needed)
            $_SESSION['user_id'] = $userId; $_SESSION['role'] = 'seeker'; $_SESSION['name'] = $name;
            header('Location: index.php?action=dashboard'); exit;
        }
        $fail('Registration failed. Please try again.');
    }

    // ── Login ─────────────────────────────────────────────────────────────────
    private function showLogin(): void {
        $this->render('seeker/login', ['error' => null, 'csrf' => $this->csrfToken()]);
    }
    private function doLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showLogin(); return; }
        $this->verifyCsrf();
        $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
        $user  = $this->model->getUserByEmail($email);
        $fail  = fn($m) => $this->render('seeker/login', ['error'=>$m,'csrf'=>$this->csrfToken()]);
        if (!$user || !password_verify($pass, $user['password_hash'])) { $fail('Invalid email or password.'); return; }
        if (!$user['is_active']) { $fail('Your account has been deactivated.'); return; }
        $_SESSION['user_id'] = $user['id']; $_SESSION['role'] = $user['role']; $_SESSION['name'] = $user['name'];
        header('Location: index.php?action=dashboard'); exit;
    }
    private function doLogout(): void {
        session_destroy(); header('Location: index.php?action=login'); exit;
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────
    private function showDashboard(): void {
        $uid = $this->userId();
        $apps = $this->model->getApplicationsBySeeker($uid);
        $saved = $this->model->getSavedJobs($uid);
        $matched = $this->model->getMatchedAlertJobs($uid);
        $this->render('seeker/dashboard', [
            'applications' => array_slice($apps, 0, 5),
            'matchedJobs'  => array_slice($matched, 0, 5),
            'stats' => [
                'total'       => count($apps),
                'shortlisted' => count(array_filter($apps, fn($a) => $a['status']==='shortlisted')),
                'interview'   => count(array_filter($apps, fn($a) => $a['status']==='interview')),
                'saved'       => count($saved),
            ],
        ]);
    }

    // ── Profile ───────────────────────────────────────────────────────────────
    private function showProfile(): void {
        $this->render('seeker/profile', [
            'user'    => $this->model->getUserById($this->userId()),
            'profile' => $this->model->getSeekerProfile($this->userId()),
            'csrf'    => $this->csrfToken(),
        ]);
    }
    private function showEditProfile(): void {
        $uid = $this->userId();
        $this->render('seeker/edit_profile', [
            'user'    => $this->model->getUserById($uid),
            'profile' => $this->model->getSeekerProfile($uid),
            'error'   => null, 'csrf' => $this->csrfToken(),
        ]);
    }
    private function saveProfile(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showEditProfile(); return; }
        $this->verifyCsrf();
        $uid      = $this->userId();
        $headline = trim($_POST['headline'] ?? '');
        $summary  = trim($_POST['summary'] ?? '');
        $skills   = trim($_POST['skills'] ?? '');
        $yrs      = (int)($_POST['years_experience'] ?? 0);
        $edu      = trim($_POST['education_level'] ?? '');
        $curr     = (float)($_POST['current_salary'] ?? 0);
        $exp      = (float)($_POST['expected_salary'] ?? 0);
        $loc      = trim($_POST['preferred_location'] ?? '');
        if (!$headline) {
            $this->render('seeker/edit_profile', [
                'error' => 'Headline is required.',
                'user'  => $this->model->getUserById($uid),
                'profile' => $this->model->getSeekerProfile($uid),
                'csrf' => $this->csrfToken(),
            ]); return;
        }
        $this->model->upsertSeekerProfile($uid, $headline, $summary, $skills, $yrs, $edu, $curr, $exp, $loc);
        $_SESSION['flash'] = 'Profile saved successfully.';
        header('Location: index.php?action=profile'); exit;
    }
    private function uploadResume(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=profile'); exit; }
        $this->verifyCsrf();
        $file = $_FILES['resume'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) { $_SESSION['flash']='Upload failed.'; header('Location: index.php?action=profile'); exit; }
        if ($file['size'] > self::MAX_RESUME_SIZE) { $_SESSION['flash']='Must be under 5 MB.'; header('Location: index.php?action=profile'); exit; }
        if (mime_content_type($file['tmp_name']) !== 'application/pdf') { $_SESSION['flash']='PDF only.'; header('Location: index.php?action=profile'); exit; }
        if (!is_dir(self::UPLOAD_DIR_RESUME)) mkdir(self::UPLOAD_DIR_RESUME, 0755, true);
        $fn = 'resume_'.$this->userId().'_'.time().'.pdf';
        if (move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR_RESUME.$fn)) {
            $this->model->updateResumePath($this->userId(), 'assets/uploads/resumes/'.$fn);
            $_SESSION['flash'] = 'Resume uploaded.';
        } else { $_SESSION['flash'] = 'Could not save file.'; }
        header('Location: index.php?action=profile'); exit;
    }
    private function uploadPic(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=profile'); exit; }
        $this->verifyCsrf();
        $file = $_FILES['profile_pic'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) { $_SESSION['flash']='Upload failed.'; header('Location: index.php?action=profile'); exit; }
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_PIC_TYPES)) { $_SESSION['flash']='JPG/PNG/WEBP only.'; header('Location: index.php?action=profile'); exit; }
        if (!is_dir(self::UPLOAD_DIR_PICS)) mkdir(self::UPLOAD_DIR_PICS, 0755, true);
        $ext = match($mime){'image/png'=>'png','image/webp'=>'webp',default=>'jpg'};
        $fn  = 'pic_'.$this->userId().'_'.time().'.'.$ext;
        if (move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR_PICS.$fn)) {
            $this->model->updateProfilePic($this->userId(), 'assets/uploads/profile_pics/'.$fn);
            $_SESSION['flash'] = 'Profile picture updated.';
        } else { $_SESSION['flash'] = 'Could not save image.'; }
        header('Location: index.php?action=profile'); exit;
    }

    // ── Jobs ──────────────────────────────────────────────────────────────────
    private function showJobs(): void {
        $keyword  = trim($_GET['q'] ?? '');
        $catId    = (int)($_GET['category'] ?? 0);
        $location = trim($_GET['location'] ?? '');
        $jobType  = trim($_GET['job_type'] ?? '');
        $expLevel = trim($_GET['exp_level'] ?? '');
        $salMin   = (float)($_GET['sal_min'] ?? 0);
        $salMax   = (float)($_GET['sal_max'] ?? 0);
        $page     = max(1, (int)($_GET['page'] ?? 1));
        [$jobs, $total] = $this->model->searchJobs($keyword,$catId,$location,$jobType,$expLevel,$salMin,$salMax,$page);
        $this->render('seeker/jobs', [
            'jobs' => $jobs, 'total' => $total, 'page' => $page,
            'categories' => $this->model->getCategories(),
            'filters' => compact('keyword','catId','location','jobType','expLevel','salMin','salMax'),
        ]);
    }
    private function showJobDetail(): void {
        $jobId = (int)($_GET['id'] ?? 0);
        $job   = $this->model->getJobById($jobId);
        if (!$job) { $this->notFound(); return; }
        $deadlinePassed = $job['deadline'] && strtotime($job['deadline']) < time();
        $this->render('seeker/job_detail', [
            'job'            => $job,
            'alreadyApplied' => $this->model->hasActiveApplication($this->userId(), $jobId),
            'deadlinePassed' => $deadlinePassed,
            'isSaved'        => $this->model->isJobSaved($this->userId(), $jobId),
            'profile'        => $this->model->getSeekerProfile($this->userId()),
            'error'          => null,
            'csrf'           => $this->csrfToken(),
        ]);
    }
    private function applyJob(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=jobs'); exit; }
        $this->verifyCsrf();
        $uid  = $this->userId();
        $jid  = (int)($_POST['job_id'] ?? 0);
        $cv   = trim($_POST['cover_letter'] ?? '');
        $job  = $this->model->getJobById($jid);
        $back = fn($msg) => $this->redirectWithFlash($msg, 'index.php?action=jobDetail&id='.$jid);
        if (!$job)                                                      { $this->notFound(); return; }
        if ($job['deadline'] && strtotime($job['deadline']) < time())   { $back('Application deadline has passed.'); return; }
        if ($this->model->hasActiveApplication($uid, $jid))             { $back('You have already applied to this job.'); return; }
        // Resume
        $resumePath = '';
        if (!empty($_FILES['resume']['name'])) {
            $f = $_FILES['resume'];
            if ($f['error'] !== UPLOAD_ERR_OK)                              { $back('Resume upload error.'); return; }
            if ($f['size'] > self::MAX_RESUME_SIZE)                         { $back('Resume must be under 5 MB.'); return; }
            if (mime_content_type($f['tmp_name']) !== 'application/pdf')    { $back('Resume must be a PDF.'); return; }
            if (!is_dir(self::UPLOAD_DIR_RESUME)) mkdir(self::UPLOAD_DIR_RESUME, 0755, true);
            $fn = 'app_'.$uid.'_'.time().'.pdf';
            if (!move_uploaded_file($f['tmp_name'], self::UPLOAD_DIR_RESUME.$fn)) { $back('Could not save resume.'); return; }
            $resumePath = 'assets/uploads/resumes/'.$fn;
        } else {
            $profile    = $this->model->getSeekerProfile($uid);
            $resumePath = $profile['resume_path'] ?? '';
            if (!$resumePath) { $back('Please upload a resume before applying.'); return; }
        }
        $appId = $this->model->applyToJob($uid, $jid, $cv, $resumePath);
        if ($appId) { $_SESSION['flash']='Application submitted!'; header('Location: index.php?action=applications'); }
        else        { $_SESSION['flash']='Submission failed. Try again.'; header('Location: index.php?action=jobDetail&id='.$jid); }
        exit;
    }
    private function withdrawApplication(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=applications'); exit; }
        $this->verifyCsrf();
        $appId = (int)($_POST['application_id'] ?? 0);
        $ok = $this->model->withdrawApplication($appId, $this->userId());
        $_SESSION['flash'] = $ok
            ? 'Application withdrawn. You may re-apply to this job.'
            : 'Could not withdraw — already reviewed by employer.';
        header('Location: index.php?action=applications'); exit;
    }
    private function showApplications(): void {
        $this->render('seeker/applications', ['applications' => $this->model->getApplicationsBySeeker($this->userId())]);
    }

    // ── Saved Jobs ────────────────────────────────────────────────────────────
    private function toggleSaveJob(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=savedJobs'); exit; }
        $this->verifyCsrf();
        $jid = (int)($_POST['job_id'] ?? 0);
        $uid = $this->userId();
        if (!$this->model->getJobById($jid)) {
            if ($this->isAjax()) { header('Content-Type: application/json'); echo json_encode(['error'=>'Not found']); exit; }
            $_SESSION['flash']='Job not found.'; header('Location: index.php?action=savedJobs'); exit;
        }
        if ($this->model->isJobSaved($uid, $jid)) { $this->model->unsaveJob($uid,$jid); $saved=false; }
        else                                       { $this->model->saveJob($uid,$jid);   $saved=true;  }
        if ($this->isAjax()) { header('Content-Type: application/json'); echo json_encode(['saved'=>$saved]); exit; }
        header('Location: '.($_SERVER['HTTP_REFERER'] ?? 'index.php?action=savedJobs')); exit;
    }
    private function showSavedJobs(): void {
        $this->render('seeker/saved_jobs', ['jobs'=>$this->model->getSavedJobs($this->userId())]);
    }

    // ── Alerts ────────────────────────────────────────────────────────────────
    private function showAlerts(): void {
        $this->render('seeker/alerts', [
            'alerts'     => $this->model->getAlertsBySeeker($this->userId()),
            'categories' => $this->model->getCategories(),
            'csrf'       => $this->csrfToken(),
        ]);
    }
    private function createAlert(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=alerts'); exit; }
        $this->verifyCsrf();
        $kw  = trim($_POST['keyword'] ?? '');
        if (!$kw) { $_SESSION['flash']='Keyword required.'; header('Location: index.php?action=alerts'); exit; }
        $this->model->createAlert($this->userId(), $kw, (int)($_POST['category_id']??0), trim($_POST['location']??''), trim($_POST['job_type']??''));
        $_SESSION['flash']='Alert created.'; header('Location: index.php?action=alerts'); exit;
    }
    private function deleteAlert(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=alerts'); exit; }
        $this->verifyCsrf();
        $this->model->deleteAlert((int)($_POST['alert_id']??0), $this->userId());
        $_SESSION['flash']='Alert deleted.'; header('Location: index.php?action=alerts'); exit;
    }

    // ── Messages ──────────────────────────────────────────────────────────────
    private function showMessages(): void {
        $uid = $this->userId();
        $msgs = $this->model->getInboxMessages($uid);
        $this->model->markAllMessagesRead($uid);
        $this->render('seeker/messages', ['messages'=>$msgs, 'csrf'=>$this->csrfToken()]);
    }
    private function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=messages'); exit; }
        $this->verifyCsrf();
        $rid = (int)($_POST['recipient_id'] ?? 0);
        $aid = (int)($_POST['application_id'] ?? 0);
        $body = trim($_POST['body'] ?? '');
        if (!$body || !$rid) { $_SESSION['flash']='Message and recipient required.'; header('Location: index.php?action=messages'); exit; }
        // IDOR fix: verify application belongs to this seeker & recipient is its employer
        if ($aid > 0 && !$this->model->verifyApplicationOwnership($aid, $this->userId(), $rid)) {
            http_response_code(403); die('<h1>403 Forbidden</h1>');
        }
        $this->model->sendMessage($this->userId(), $rid, $aid, $body);
        $_SESSION['flash']='Message sent.'; header('Location: index.php?action=messages'); exit;
    }

    // ── Outreach ──────────────────────────────────────────────────────────────
    private function showOutreach(): void {
        $this->render('seeker/outreach', ['outreach'=>$this->model->getRecruiterOutreach($this->userId()), 'csrf'=>$this->csrfToken()]);
    }
    private function respondOutreach(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=outreach'); exit; }
        $this->verifyCsrf();
        $oid    = (int)($_POST['outreach_id'] ?? 0);
        $status = in_array($_POST['status']??'', ['read','responded']) ? $_POST['status'] : 'responded';
        $this->model->updateOutreachStatus($oid, $this->userId(), $status);
        header('Location: index.php?action=outreach'); exit;
    }

    // ── Complaints ────────────────────────────────────────────────────────────
    private function showComplaintForm(): void {
        $this->render('seeker/complaint', ['subjectId'=>(int)($_GET['subject_id']??0), 'error'=>null, 'csrf'=>$this->csrfToken()]);
    }
    private function submitComplaint(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=jobs'); exit; }
        $this->verifyCsrf();
        $sid  = (int)($_POST['subject_id'] ?? 0);
        $desc = trim($_POST['description'] ?? '');
        if (!$desc || !$sid) { $this->render('seeker/complaint', ['subjectId'=>$sid,'error'=>'Please fill in all fields.','csrf'=>$this->csrfToken()]); return; }
        $this->model->submitComplaint($this->userId(), $sid, $desc);
        $_SESSION['flash']='Complaint submitted.'; header('Location: index.php?action=dashboard'); exit;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function render(string $view, array $data = []): void {
        extract($data);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require __DIR__ . '/../views/' . $view . '.php';
    }
    private function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    private function redirectWithFlash(string $msg, string $url): void {
        $_SESSION['flash'] = $msg; header('Location: '.$url); exit;
    }
    private function notFound(): void {
        http_response_code(404); echo '<h1>404 — Page not found</h1>'; exit;
    }
}
