<?php


session_start();

require_once __DIR__ . '/../models/SeekerModel.php';

class SeekerController {

    private SeekerModel $model;

    // Upload config
    private const UPLOAD_DIR_RESUME  = __DIR__ . '/../assets/uploads/resumes/';
    private const UPLOAD_DIR_PICS    = __DIR__ . '/../assets/uploads/profile_pics/';
    private const MAX_RESUME_SIZE    = 5 * 1024 * 1024; // 5 MB
    private const ALLOWED_PIC_TYPES  = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct() {
        $this->model = new SeekerModel();
    }


    public function dispatch(string $action): void {
        // Public actions
        $public = ['login', 'register', 'doLogin', 'doRegister'];
        if (!in_array($action, $public)) {
            $this->requireAuth();
        }

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


    private function requireAuth(): void {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
            header('Location: /job_portal/seeker/index.php?action=login');
            exit;
        }
    }

    private function userId(): int {
        return (int)$_SESSION['user_id'];
    }



    private function showRegister(): void {
        $this->render('seeker/register', ['error' => null]);
    }

    private function doRegister(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showRegister(); return; }

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['confirm_pass']  ?? '';

        // Validation
        if (!$name || !$email || !$phone || !$password) {
            $this->render('seeker/register', ['error' => 'All fields are required.']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('seeker/register', ['error' => 'Invalid email address.']);
            return;
        }
        if (strlen($password) < 6) {
            $this->render('seeker/register', ['error' => 'Password must be at least 6 characters.']);
            return;
        }
        if ($password !== $confirm) {
            $this->render('seeker/register', ['error' => 'Passwords do not match.']);
            return;
        }
        if ($this->model->emailExists($email)) {
            $this->render('seeker/register', ['error' => 'Email already registered.']);
            return;
        }

        $hash   = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->model->registerUser($name, $email, $phone, $hash);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['role']    = 'seeker';
            $_SESSION['name']    = $name;
            header('Location: /job_portal/seeker/index.php?action=dashboard');
            exit;
        }
        $this->render('seeker/register', ['error' => 'Registration failed. Please try again.']);
    }

    // ── Login 
    private function showLogin(): void {
        $this->render('seeker/login', ['error' => null]);
    }

    private function doLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showLogin(); return; }

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        $user = $this->model->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->render('seeker/login', ['error' => 'Invalid email or password.']);
            return;
        }
        if (!$user['is_active']) {
            $this->render('seeker/login', ['error' => 'Your account has been deactivated.']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];
        header('Location: /job_portal/seeker/index.php?action=dashboard');
        exit;
    }

    private function doLogout(): void {
        session_destroy();
        header('Location: /job_portal/seeker/index.php?action=login');
        exit;
    }

    // ── Dashboard

    private function showDashboard(): void {
        $userId       = $this->userId();
        $applications = $this->model->getApplicationsBySeeker($userId);
        $savedJobs    = $this->model->getSavedJobs($userId);
        $matchedJobs  = $this->model->getMatchedAlertJobs($userId);

        // Summary counts
        $stats = [
            'total'       => count($applications),
            'shortlisted' => count(array_filter($applications, fn($a) => $a['status'] === 'shortlisted')),
            'interview'   => count(array_filter($applications, fn($a) => $a['status'] === 'interview')),
            'saved'       => count($savedJobs),
        ];

        $this->render('seeker/dashboard', [
            'applications' => array_slice($applications, 0, 5),
            'matchedJobs'  => array_slice($matchedJobs, 0, 5),
            'stats'        => $stats,
        ]);
    }

    
    private function showProfile(): void {
        $user    = $this->model->getUserById($this->userId());
        $profile = $this->model->getSeekerProfile($this->userId());
        $this->render('seeker/profile', ['user' => $user, 'profile' => $profile]);
    }

    private function showEditProfile(): void {
        $user    = $this->model->getUserById($this->userId());
        $profile = $this->model->getSeekerProfile($this->userId());
        $this->render('seeker/edit_profile', ['user' => $user, 'profile' => $profile, 'error' => null]);
    }

    private function saveProfile(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->showEditProfile(); return; }

        $userId   = $this->userId();
        $headline = trim($_POST['headline']          ?? '');
        $summary  = trim($_POST['summary']           ?? '');
        $skills   = trim($_POST['skills']            ?? '');
        $yearsExp = (int)($_POST['years_experience'] ?? 0);
        $eduLevel = trim($_POST['education_level']   ?? '');
        $currSal  = (float)($_POST['current_salary'] ?? 0);
        $expSal   = (float)($_POST['expected_salary'] ?? 0);
        $prefLoc  = trim($_POST['preferred_location'] ?? '');

        if (!$headline) {
            $this->render('seeker/edit_profile', [
                'error'   => 'Headline is required.',
                'user'    => $this->model->getUserById($userId),
                'profile' => $this->model->getSeekerProfile($userId),
            ]);
            return;
        }

        $this->model->upsertSeekerProfile(
            $userId, $headline, $summary, $skills,
            $yearsExp, $eduLevel, $currSal, $expSal, $prefLoc
        );

        header('Location: /job_portal/seeker/index.php?action=profile&saved=1');
        exit;
    }

    private function uploadResume(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=profile'); exit; }

        $file = $_FILES['resume'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Upload failed.';
            header('Location: /job_portal/seeker/index.php?action=profile');
            exit;
        }
        if ($file['size'] > self::MAX_RESUME_SIZE) {
            $_SESSION['flash'] = 'Resume must be under 5 MB.';
            header('Location: /job_portal/seeker/index.php?action=profile');
            exit;
        }
        if (mime_content_type($file['tmp_name']) !== 'application/pdf') {
            $_SESSION['flash'] = 'Only PDF files are accepted.';
            header('Location: /job_portal/seeker/index.php?action=profile');
            exit;
        }

        if (!is_dir(self::UPLOAD_DIR_RESUME)) mkdir(self::UPLOAD_DIR_RESUME, 0755, true);
        $filename = 'resume_' . $this->userId() . '_' . time() . '.pdf';
        $dest     = self::UPLOAD_DIR_RESUME . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $this->model->updateResumePath($this->userId(), 'assets/uploads/resumes/' . $filename);
            $_SESSION['flash'] = 'Resume uploaded successfully.';
        } else {
            $_SESSION['flash'] = 'Could not save file.';
        }
        header('Location: /job_portal/seeker/index.php?action=profile');
        exit;
    }

    private function uploadPic(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=profile'); exit; }

        $file = $_FILES['profile_pic'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Upload failed.';
            header('Location: /job_portal/seeker/index.php?action=profile');
            exit;
        }
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_PIC_TYPES)) {
            $_SESSION['flash'] = 'Only JPG/PNG/WEBP images accepted.';
            header('Location: /job_portal/seeker/index.php?action=profile');
            exit;
        }

        if (!is_dir(self::UPLOAD_DIR_PICS)) mkdir(self::UPLOAD_DIR_PICS, 0755, true);
        $ext      = match($mime) { 'image/png' => 'png', 'image/webp' => 'webp', default => 'jpg' };
        $filename = 'pic_' . $this->userId() . '_' . time() . '.' . $ext;
        $dest     = self::UPLOAD_DIR_PICS . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $this->model->updateProfilePic($this->userId(), 'assets/uploads/profile_pics/' . $filename);
            $_SESSION['flash'] = 'Profile picture updated.';
        } else {
            $_SESSION['flash'] = 'Could not save image.';
        }
        header('Location: /job_portal/seeker/index.php?action=profile');
        exit;
    }

    // ── Job Search 

    private function showJobs(): void {
        $keyword  = trim($_GET['q']         ?? '');
        $catId    = (int)($_GET['category'] ?? 0);
        $location = trim($_GET['location']  ?? '');
        $jobType  = trim($_GET['job_type']  ?? '');
        $expLevel = trim($_GET['exp_level'] ?? '');
        $salMin   = (float)($_GET['sal_min'] ?? 0);
        $salMax   = (float)($_GET['sal_max'] ?? 0);

        $jobs       = $this->model->searchJobs($keyword, $catId, $location, $jobType, $expLevel, $salMin, $salMax);
        $categories = $this->model->getCategories();

        $this->render('seeker/jobs', [
            'jobs'       => $jobs,
            'categories' => $categories,
            'filters'    => compact('keyword', 'catId', 'location', 'jobType', 'expLevel', 'salMin', 'salMax'),
        ]);
    }

    private function showJobDetail(): void {
        $jobId = (int)($_GET['id'] ?? 0);
        $job   = $this->model->getJobById($jobId);
        if (!$job) { $this->notFound(); return; }

        $alreadyApplied = $this->model->hasApplied($this->userId(), $jobId);
        $isSaved        = $this->model->isJobSaved($this->userId(), $jobId);
        $profile        = $this->model->getSeekerProfile($this->userId());

        $this->render('seeker/job_detail', [
            'job'            => $job,
            'alreadyApplied' => $alreadyApplied,
            'isSaved'        => $isSaved,
            'profile'        => $profile,
            'error'          => null,
        ]);
    }

    private function applyJob(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=jobs'); exit; }

        $userId      = $this->userId();
        $jobId       = (int)($_POST['job_id'] ?? 0);
        $coverLetter = trim($_POST['cover_letter'] ?? '');
        $job         = $this->model->getJobById($jobId);

        if (!$job) { $this->notFound(); return; }
        if ($this->model->hasApplied($userId, $jobId)) {
            $_SESSION['flash'] = 'You have already applied to this job.';
            header('Location: /job_portal/seeker/index.php?action=jobDetail&id=' . $jobId);
            exit;
        }

        // Handle resume: use profile resume OR new upload
        $resumePath = '';
        if (!empty($_FILES['resume']['name'])) {
            // New upload
            $file = $_FILES['resume'];
            if ($file['error'] === UPLOAD_ERR_OK
                && $file['size'] <= self::MAX_RESUME_SIZE
                && mime_content_type($file['tmp_name']) === 'application/pdf'
            ) {
                if (!is_dir(self::UPLOAD_DIR_RESUME)) mkdir(self::UPLOAD_DIR_RESUME, 0755, true);
                $filename   = 'app_resume_' . $userId . '_' . time() . '.pdf';
                $dest       = self::UPLOAD_DIR_RESUME . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $resumePath = 'assets/uploads/resumes/' . $filename;
                }
            }
        } else {
            // Use profile resume
            $profile    = $this->model->getSeekerProfile($userId);
            $resumePath = $profile['resume_path'] ?? '';
        }

        $appId = $this->model->applyToJob($userId, $jobId, $coverLetter, $resumePath);
        if ($appId) {
            $_SESSION['flash'] = 'Application submitted successfully!';
            header('Location: /job_portal/seeker/index.php?action=applications');
        } else {
            $_SESSION['flash'] = 'Failed to submit application.';
            header('Location: /job_portal/seeker/index.php?action=jobDetail&id=' . $jobId);
        }
        exit;
    }

    private function withdrawApplication(): void {
        $appId = (int)($_POST['application_id'] ?? 0);
        $ok    = $this->model->withdrawApplication($appId, $this->userId());
        $_SESSION['flash'] = $ok
            ? 'Application withdrawn.'
            : 'Could not withdraw (it may have already been reviewed).';
        header('Location: /job_portal/seeker/index.php?action=applications');
        exit;
    }

    private function showApplications(): void {
        $applications = $this->model->getApplicationsBySeeker($this->userId());
        $this->render('seeker/applications', ['applications' => $applications]);
    }

    // ── Saved Jobs

    private function toggleSaveJob(): void {
        $jobId  = (int)($_POST['job_id'] ?? 0);
        $userId = $this->userId();

        if ($this->model->isJobSaved($userId, $jobId)) {
            $this->model->unsaveJob($userId, $jobId);
            $saved = false;
        } else {
            $this->model->saveJob($userId, $jobId);
            $saved = true;
        }

        // Respond as JSON if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['saved' => $saved]);
            exit;
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/job_portal/seeker/index.php?action=savedJobs'));
        exit;
    }

    private function showSavedJobs(): void {
        $jobs = $this->model->getSavedJobs($this->userId());
        $this->render('seeker/saved_jobs', ['jobs' => $jobs]);
    }

    // ── Alerts 

    private function showAlerts(): void {
        $alerts     = $this->model->getAlertsBySeeker($this->userId());
        $categories = $this->model->getCategories();
        $this->render('seeker/alerts', ['alerts' => $alerts, 'categories' => $categories]);
    }

    private function createAlert(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=alerts'); exit; }

        $keyword  = trim($_POST['keyword']     ?? '');
        $catId    = (int)($_POST['category_id'] ?? 0);
        $location = trim($_POST['location']    ?? '');
        $jobType  = trim($_POST['job_type']    ?? '');

        $this->model->createAlert($this->userId(), $keyword, $catId, $location, $jobType);
        header('Location: /job_portal/seeker/index.php?action=alerts');
        exit;
    }

    private function deleteAlert(): void {
        $alertId = (int)($_POST['alert_id'] ?? 0);
        $this->model->deleteAlert($alertId, $this->userId());
        header('Location: /job_portal/seeker/index.php?action=alerts');
        exit;
    }

    // ── Messages 

    private function showMessages(): void {
        $userId   = $this->userId();
        $messages = $this->model->getInboxMessages($userId);
        // Auto-mark all unread messages as read when inbox is opened
        $this->model->markAllMessagesRead($userId);
        $this->render('seeker/messages', ['messages' => $messages]);
    }

    private function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=messages'); exit; }

        $recipientId   = (int)($_POST['recipient_id']   ?? 0);
        $applicationId = (int)($_POST['application_id'] ?? 0);
        $body          = trim($_POST['body']            ?? '');

        if (!$body || !$recipientId) {
            header('Location: /job_portal/seeker/index.php?action=messages');
            exit;
        }
        $this->model->sendMessage($this->userId(), $recipientId, $applicationId, $body);
        header('Location: /job_portal/seeker/index.php?action=messages');
        exit;
    }

    private function showOutreach(): void {
        $outreach = $this->model->getRecruiterOutreach($this->userId());
        $this->render('seeker/outreach', ['outreach' => $outreach]);
    }

    private function respondOutreach(): void {
        $outreachId = (int)($_POST['outreach_id'] ?? 0);
        $status     = $_POST['status'] ?? 'responded';
        $allowed    = ['read', 'responded'];
        if (!in_array($status, $allowed)) $status = 'responded';
        $this->model->updateOutreachStatus($outreachId, $this->userId(), $status);
        header('Location: /job_portal/seeker/index.php?action=outreach');
        exit;
    }

    // ── Complaints 
    private function showComplaintForm(): void {
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $this->render('seeker/complaint', ['subjectId' => $subjectId, 'error' => null]);
    }

    private function submitComplaint(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /job_portal/seeker/index.php?action=jobs'); exit; }

        $subjectId   = (int)($_POST['subject_id']  ?? 0);
        $description = trim($_POST['description']  ?? '');

        if (!$description || !$subjectId) {
            $this->render('seeker/complaint', ['subjectId' => $subjectId, 'error' => 'Please fill in all fields.']);
            return;
        }
        $this->model->submitComplaint($this->userId(), $subjectId, $description);
        $_SESSION['flash'] = 'Complaint submitted. Admin will review it.';
        header('Location: /job_portal/seeker/index.php?action=dashboard');
        exit;
    }

    // ── Helpers 

    /** Load a view file and pass data to it */
    private function render(string $view, array $data = []): void {
        extract($data); // makes $data keys available as variables in view
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    private function notFound(): void {
        http_response_code(404);
        echo '<h1>404 — Page not found</h1>';
        exit;
    }
}