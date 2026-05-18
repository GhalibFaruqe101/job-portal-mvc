# Admin Module - Job Portal

This module implements the **Admin** role requirements for the Job Portal project.

## Folder placement

Copy these folders into the project root:

```text
/admin
/public/css/admin
/public/js/admin
```

Do not modify other members' folders. The admin module keeps its own MVC files inside `/admin` and only uses `/public/css/admin` and `/public/js/admin` for admin-specific assets.

## Database setup

1. Import the team's original database file first: `job_portal_db.sql`.
2. Import this file next:

```text
admin/install/admin_module_patch.sql
```

This patch adds the small extra tables needed for full Admin requirements:

- `platform_policies`
- `announcements`
- `admin_action_logs`

It also inserts a default admin account:

```text
Email: admin@jobportal.test
Password: admin123
```

Optional demo data:

```text
admin/install/admin_demo_data.sql
```

Use the optional demo data only when you need sample employers, recruiters, jobs, applications and complaints for presentation/demo.

## Run URL

With XAMPP, place the project in `htdocs/job_portal`, then open:

```text
http://localhost/job_portal/admin/index.php?action=login
```

## Implemented Admin requirements

- Admin login/dashboard
- Total users by role
- Total active jobs
- Today's applications
- Pending verification requests
- Employer approval/rejection/suspension/reactivation
- Recruiter approval/rejection/suspension/reactivation
- Seeker search/view/deactivate/reactivate
- Category add/rename/delete with safety checks
- All job postings search/filter by status, employer, recruiter
- Policy-violating job removal
- Featured job management with AJAX
- Complaint review, admin note, resolve with AJAX
- Platform policy settings
- Platform analytics
- User growth report
- Platform-wide announcements
- Monthly platform summary report and CSV export

## AJAX features

The following admin actions use `XMLHttpRequest` and JSON API endpoints:

- Account approve/reject/suspend/reactivate: `admin/api/account_action.php`
- Featured job toggle: `admin/api/featured_toggle.php`
- Complaint resolution: `admin/api/complaint_resolve.php`

## Notes

All database actions in the Admin model use `mysqli` prepared statements. Protected pages use the standard session keys:

```php
$_SESSION['user_id']
$_SESSION['role']
```

The module sets `$_SESSION['role'] = 'admin'` after successful login.
