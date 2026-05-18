# Employer Module 
> This document defines the responsibilities, boundaries, workflow, and execution order for AI agents working on the Employer Module of the Job Portal project.

The implementation MUST strictly follow:
- Team integration rules
- MVC architecture
- Shared RBAC/session contract
- Shared database schema
- Shared UI structure
- Employer-only workspace restrictions

---

# 1. Objective

The purpose of this document is to:

- Split the employer module into manageable agent tasks
- Prevent architecture conflicts
- Maintain coding consistency
- Ensure team integration compatibility
- Speed up parallel development

---

# 2. Global Project Constraints (MANDATORY)

All agents MUST follow these rules.

---

## Architecture Rules

STRICTLY follow:

```text
MVC Architecture
```

### Controllers

Responsible for:
- Request handling
- Validation
- Flow control
- Calling models

---

### Models

Responsible for:
- Database queries
- CRUD operations
- Business logic

---

### Views

Responsible ONLY for:
- HTML
- Forms
- Rendering UI

---

## FORBIDDEN

Do NOT:
- Write SQL inside views
- Write heavy business logic inside views
- Access another module's directories

---

# 3. Allowed Workspace

Agents may ONLY modify:

```text
/employer
/public/css/employer
/public/js/employer
```

---

# 4. Shared Session Contract (STRICT)

 MUST use:

```php
$_SESSION['user_id']
$_SESSION['role']
```

---

## Required Role Value

```php
'employer'
```

No alternative naming is allowed.

---

# 5. Shared Database Rules

## REQUIRED

Use:

```php
mysqli + prepared statements
```

---

## FORBIDDEN

```php
$sql = "SELECT * FROM users WHERE email = '$email'";
```

Always use prepared statements.

---

# 6. Shared UI Rules

All pages MUST include:

```html
<link rel="stylesheet" href="../../public/css/style.css">
```

---

Employer-specific CSS:

```html
<link rel="stylesheet" href="../../public/css/employer/dashboard.css">
```

---

# 7. Agent Structure

The employer module is divided into specialized AI agents.

---

# AGENT 1 — Authentication & Session Agent

## Responsibility

Implement:
- Registration
- Login
- Logout
- Session management
- RBAC protection

---

## Files Responsible For

### Controllers

```text
/employer/controllers/AuthController.php
```

---

### Models

```text
/employer/models/UserModel.php
/employer/models/EmployerModel.php
```

---

### Views

```text
/employer/views/login.php
/employer/views/register.php
```

---

### Helpers

```text
/employer/helpers/session.php
```

---

## Features To Implement

### Registration

- Create employer account
- Store hashed password
- Validate unique email
- Validate required fields

---

### Login

- Authenticate employer
- Create sessions
- Redirect to dashboard

---

### Logout

- Destroy session
- Redirect to login

---

## Security Requirements

MANDATORY:

```php
password_hash()
password_verify()
```

---

# AGENT 2 — Employer Profile Agent

## Responsibility

Implement employer/company profile management.

---

## Files Responsible For

### Controllers

```text
/employer/controllers/EmployerController.php
```

---

### Models

```text
/employer/models/EmployerModel.php
```

---

### Views

```text
/employer/views/profile.php
```

---

## Features To Implement

Employer can manage:

- Company name
- Industry
- Company size
- Description
- Website
- Address
- Logo upload

---

## File Upload Rules

Validate:

- File size
- Image format
- MIME type

Rename files securely.

---

# AGENT 3 — Job CRUD Agent

## Responsibility

Implement complete job management system.

---

## Files Responsible For

### Controllers

```text
/employer/controllers/JobController.php
```

---

### Models

```text
/employer/models/JobModel.php
```

---

### Views

```text
/employer/views/create-job.php
/employer/views/manage-jobs.php
```

---

## Features To Implement

### Create Job

Required fields:

```text
Title
Category
Description
Requirements
Benefits
Salary Min
Salary Max
Location
Job Type
Experience Level
Deadline
```

---

### Job Actions

- Create
- Edit
- Delete
- Close
- Reopen
- Draft save

---

## Required Status Values

```text
Draft
Active
Closed
```

---

# AGENT 4 — Application Management Agent

## Responsibility

Implement application review workflow.

---

## Files Responsible For

### Controllers

```text
/employer/controllers/ApplicationController.php
```

---

### Models

```text
/employer/models/ApplicationModel.php
```

---

### Views

```text
/employer/views/applications.php
```

---

## Features To Implement

Employer can:

- View applicants
- Filter applications
- Read cover letters
- Download resumes
- Update status

---

## Required Status Values

```text
Reviewed
Shortlisted
Interview
Rejected
```

---

# AGENT 5 — AJAX & API Agent

## Responsibility

Implement all AJAX-based dynamic interactions.

---

## Files Responsible For

### JavaScript

```text
/public/js/employer/
```

---

### API Endpoints

```text
/employer/api/
```

---

## Mandatory AJAX Requirement

Use:

- XMLHttpRequest
- PHP endpoint
- JSON response

---

## Recommended AJAX Feature

### Dynamic Application Status Update

Flow:

```text
Dropdown Change
    ↓
AJAX Request
    ↓
PHP Endpoint
    ↓
JSON Response
    ↓
Update UI Without Reload
```

---

## Alternative Feature

Toggle:

```text
Active ↔ Closed
```

Without page refresh.

---

# AGENT 6 — Dashboard & Analytics Agent

## Responsibility

Implement employer dashboard and analytics.

---

## Files Responsible For

### Controllers

```text
/employer/controllers/AnalyticsController.php
```

---

### Models

```text
/employer/models/AnalyticsModel.php
```

---

### Views

```text
/employer/views/dashboard.php
/employer/views/analytics.php
```

---

## Dashboard Features

Display:

- Total jobs
- Active jobs
- Closed jobs
- Total applications
- Recent applicants

---

## Analytics Features

Display:

- Shortlisted count
- Interview count
- Hiring conversion rate
- Applications over time

---

# AGENT 7 — Messaging & Complaints Agent

## Responsibility

Implement employer communication system.

---

## Files Responsible For

### Controllers

```text
/employer/controllers/MessageController.php
/employer/controllers/ComplaintController.php
```

---

### Models

```text
/employer/models/MessageModel.php
/employer/models/ComplaintModel.php
```

---

### Views

```text
/employer/views/messages.php
/employer/views/complaints.php
```

---

## Features To Implement

### Messaging

Employer can:

- Send interview invitations
- Send rejection notices
- Send follow-up messages

---

### Complaint System

Employer can submit complaints against:

- Recruiters
- Job seekers

---

# 8. Integration Rules

Before merging:

Each agent MUST ensure:

- No file conflicts
- Shared session keys unchanged
- Shared CSS untouched
- Controllers use models correctly
- Prepared statements used everywhere
- RBAC enforced on protected pages

---

# 9. Shared Database Tables

## users

```sql
id,
name,
email,
password_hash,
phone,
role,
profile_pic,
is_active,
is_verified,
created_at
```

---

## employer_profiles

```sql
id,
user_id,
company_name,
industry,
company_size,
description,
website,
address,
logo_path
```

---

## jobs

```sql
id,
employer_id,
recruiter_id,
category_id,
title,
description,
requirements,
benefits,
salary_min,
salary_max,
location,
job_type,
experience_level,
deadline,
status,
is_featured,
created_at
```

---

## applications

```sql
id,
job_id,
seeker_id,
recruiter_id,
cover_letter,
resume_path,
status,
applied_at
```

---

## messages

```sql
id,
sender_id,
recipient_id,
application_id,
body,
sent_at,
is_read
```

---

# 10. Git Workflow Rules

STRICTLY FOLLOW:

---

## Branching

Use:

```text
feature/employer-auth
feature/employer-jobs
feature/employer-dashboard
feature/employer-ajax
```

---

## Commit Message Format

```text
Added employer login validation
Implemented job CRUD
Added AJAX application status update
```

---

## Forbidden

Do NOT:

- Push directly to main
- Commit broken code
- Rename shared directories

---

# 11. Recommended Development Order

## Phase 1

- DB setup
- Authentication
- Session helper

---

## Phase 2

- Employer profile
- Dashboard

---

## Phase 3

- Job CRUD
- Application management

---

## Phase 4

- AJAX integration
- Analytics
- Messaging
- Complaints

---

# 12. Final Validation Checklist

Before final merge:

- MVC structure followed
- Employer-only workspace respected
- RBAC working
- Sessions working
- AJAX feature functioning
- No SQL inside views
- Prepared statements used
- XAMPP compatib