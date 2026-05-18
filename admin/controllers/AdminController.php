<?php

require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    private AdminModel $model;

    public function __construct() {
        $this->model = new AdminModel();
    }

    public function dispatch(string $action): void {
        $publicActions = ['login', 'authenticate'];
        if (!in_array($action, $publicActions, true)) {
            require_role('admin');
        }

        switch ($action) {
            case 'login': $this->login(); break;
            case 'authenticate': $this->authenticate(); break;
            case 'logout': $this->logout(); break;
            case 'dashboard': $this->dashboard(); break;
            case 'employers': $this->accounts('employer', 'employers'); break;
            case 'recruiters': $this->accounts('recruiter', 'recruiters'); break;
            case 'seekers': $this->accounts('seeker', 'seekers'); break;
            case 'userView': $this->userView(); break;
            case 'categories': $this->categories(); break;
            case 'saveCategory': $this->saveCategory(); break;
            case 'deleteCategory': $this->deleteCategory(); break;
            case 'jobs': $this->jobs(); break;
            case 'saveJobStatus': $this->saveJobStatus(); break;
            case 'deleteJob': $this->deleteJob(); break;
            case 'featured': $this->featured(); break;
            case 'complaints': $this->complaints(); break;
            case 'policies': $this->policies(); break;
            case 'savePolicies': $this->savePolicies(); break;
            case 'analytics': $this->analytics(); break;
            case 'growth': $this->growth(); break;
            case 'announcements': $this->announcements(); break;
            case 'saveAnnouncement': $this->saveAnnouncement(); break;
            case 'toggleAnnouncement': $this->toggleAnnouncement(); break;
            case 'monthlyReport': $this->monthlyReport(); break;
            case 'exportMonthlyReport': $this->exportMonthlyReport(); break;
            default: $this->dashboard(); break;
        }
    }

    private function login(): void {
        if (is_logged_in() && $_SESSION['role'] === 'admin') {
            header('Location: index.php?action=dashboard');
            exit;
        }
        $this->render('login', ['error' => null, 'pageTitle' => 'Admin Login']);
    }

    private function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit;
        }
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->render('login', ['error' => 'Email and password are required.', 'pageTitle' => 'Admin Login']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('login', ['error' => 'Please enter a valid email address.', 'pageTitle' => 'Admin Login']);
            return;
        }

        try {
            $admin = $this->model->authenticateAdmin($email, $password);
        } catch (Throwable $e) {
            $this->render('login', ['error' => 'Database error: ' . $e->getMessage(), 'pageTitle' => 'Admin Login']);
            return;
        }

        if (!$admin) {
            $this->render('login', ['error' => 'Invalid admin email or password.', 'pageTitle' => 'Admin Login']);
            return;
        }
        if (isset($admin['error']) && $admin['error'] === 'inactive') {
            $this->render('login', ['error' => 'This admin account is inactive.', 'pageTitle' => 'Admin Login']);
            return;
        }

        $_SESSION['user_id'] = (int)$admin['id'];
        $_SESSION['role'] = 'admin';
        $_SESSION['name'] = $admin['name'];
        admin_csrf_token();
        header('Location: index.php?action=dashboard');
        exit;
    }

    private function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    private function dashboard(): void {
        $this->safeRender('dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'activeNav' => 'dashboard',
            'stats' => $this->model->dashboardStats(),
            'pendingAccounts' => $this->model->pendingAccounts(),
            'recentComplaints' => $this->model->recentComplaints(),
            'logs' => $this->model->adminActionLogs(10),
        ]);
    }

    private function accounts(string $role, string $view): void {
        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
        $this->safeRender($view, [
            'pageTitle' => ucfirst($view) . ' Management',
            'activeNav' => $view,
            'accounts' => $this->model->listAccounts($role, $status, $search),
            'status' => $status,
            'search' => $search,
        ]);
    }

    private function userView(): void {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->model->getUserDetails($id);
        if (!$user) {
            $this->safeRender('user_view', [
                'pageTitle' => 'User Details',
                'activeNav' => 'users',
                'user' => null,
                'error' => 'User not found.',
            ]);
            return;
        }
        $this->safeRender('user_view', [
            'pageTitle' => 'User Details',
            'activeNav' => $user['role'] . 's',
            'user' => $user,
            'error' => null,
        ]);
    }

    private function categories(): void {
        $this->safeRender('categories', [
            'pageTitle' => 'Job Categories',
            'activeNav' => 'categories',
            'categories' => $this->model->categoriesWithCounts(),
            'message' => $_GET['message'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    private function saveCategory(): void {
        $this->requirePostWithCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $result = $id > 0
            ? $this->model->renameCategory($id, $name, $description)
            : $this->model->createCategory($name, $description);
        $this->redirectWithResult('categories', $result);
    }

    private function deleteCategory(): void {
        $this->requirePostWithCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->model->deleteCategory($id);
        $this->redirectWithResult('categories', $result);
    }

    private function jobs(): void {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'employer_id' => (int)($_GET['employer_id'] ?? 0),
            'recruiter_id' => (int)($_GET['recruiter_id'] ?? 0),
            'search' => trim($_GET['search'] ?? ''),
        ];
        $this->safeRender('jobs', [
            'pageTitle' => 'All Job Postings',
            'activeNav' => 'jobs',
            'jobs' => $this->model->listJobs($filters),
            'filters' => $filters,
            'options' => $this->model->jobFilterOptions(),
            'message' => $_GET['message'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    private function saveJobStatus(): void {
        $this->requirePostWithCsrf();
        $jobId = (int)($_POST['job_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $result = $this->model->updateJobStatus($jobId, $status);
        $this->redirectWithResult('jobs', $result);
    }

    private function deleteJob(): void {
        $this->requirePostWithCsrf();
        $jobId = (int)($_POST['job_id'] ?? 0);
        $result = $this->model->deleteJob($jobId);
        $this->redirectWithResult('jobs', $result);
    }

    private function featured(): void {
        $filters = ['featured' => 1, 'search' => trim($_GET['search'] ?? '')];
        $this->safeRender('featured', [
            'pageTitle' => 'Featured Jobs',
            'activeNav' => 'featured',
            'jobs' => $this->model->listJobs($filters),
            'search' => $filters['search'],
        ]);
    }

    private function complaints(): void {
        $status = $_GET['status'] ?? 'all';
        $this->safeRender('complaints', [
            'pageTitle' => 'Complaints',
            'activeNav' => 'complaints',
            'complaints' => $this->model->listComplaints($status),
            'status' => $status,
        ]);
    }

    private function policies(): void {
        $this->safeRender('policies', [
            'pageTitle' => 'Platform Policies',
            'activeNav' => 'policies',
            'policies' => $this->model->policies(),
            'message' => $_GET['message'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    private function savePolicies(): void {
        $this->requirePostWithCsrf();
        $result = $this->model->savePolicies($_POST);
        $this->redirectWithResult('policies', $result);
    }

    private function analytics(): void {
        $this->safeRender('analytics', [
            'pageTitle' => 'Platform Analytics',
            'activeNav' => 'analytics',
            'analytics' => $this->model->platformAnalytics(),
        ]);
    }

    private function growth(): void {
        $this->safeRender('growth', [
            'pageTitle' => 'User Growth Report',
            'activeNav' => 'growth',
            'growth' => $this->model->userGrowthReport(),
        ]);
    }

    private function announcements(): void {
        $this->safeRender('announcements', [
            'pageTitle' => 'Announcements',
            'activeNav' => 'announcements',
            'announcements' => $this->model->listAnnouncements(),
            'message' => $_GET['message'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    private function saveAnnouncement(): void {
        $this->requirePostWithCsrf();
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $isActive = isset($_POST['is_active']);
        $result = $this->model->createAnnouncement((int)$_SESSION['user_id'], $title, $body, $isActive);
        $this->redirectWithResult('announcements', $result);
    }

    private function toggleAnnouncement(): void {
        $this->requirePostWithCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->model->toggleAnnouncement($id);
        $this->redirectWithResult('announcements', $result);
    }

    private function monthlyReport(): void {
        $month = $_GET['month'] ?? date('Y-m');
        $this->safeRender('monthly_report', [
            'pageTitle' => 'Monthly Summary Report',
            'activeNav' => 'monthly',
            'month' => $month,
            'summary' => $this->model->monthlySummary($month),
        ]);
    }

    private function exportMonthlyReport(): void {
        $month = $_GET['month'] ?? date('Y-m');
        $summary = $this->model->monthlySummary($month);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="platform-summary-' . preg_replace('/[^0-9\-]/', '', $summary['month']) . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Metric', 'Value']);
        foreach ($summary as $key => $value) {
            fputcsv($out, [ucwords(str_replace('_', ' ', $key)), $value]);
        }
        fclose($out);
        exit;
    }

    private function requirePostWithCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_admin_csrf($_POST['csrf_token'] ?? '')) {
            die('Invalid request token. Please go back and try again.');
        }
    }

    private function redirectWithResult(string $action, array $result): void {
        $key = !empty($result['ok']) ? 'message' : 'error';
        $msg = urlencode($result['message'] ?? 'Action completed.');
        header("Location: index.php?action={$action}&{$key}={$msg}");
        exit;
    }

    private function safeRender(string $view, array $data): void {
        try {
            $this->render($view, $data);
        } catch (Throwable $e) {
            $this->render('error', [
                'pageTitle' => 'Admin Error',
                'activeNav' => 'dashboard',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function render(string $view, array $data = []): void {
        $data['assetBase'] = $data['assetBase'] ?? '../';
        $data['csrfToken'] = admin_csrf_token();
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
?>
