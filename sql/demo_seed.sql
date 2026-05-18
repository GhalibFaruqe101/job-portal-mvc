-- ============================================================
-- Demo Seed Data for job_portal_db
-- Run this AFTER importing the main sql/job_portal_db.sql
-- ============================================================

USE job_portal_db;

-- ─── 1. Categories ───────────────────────────────────────────
INSERT IGNORE INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Technology',    'Software, IT, and Engineering roles'),
(2, 'Finance',       'Accounting, banking, and finance roles'),
(3, 'Healthcare',    'Medical and healthcare positions'),
(4, 'Marketing',     'Marketing, advertising, and communications'),
(5, 'Design',        'UI/UX, graphic design, and creative roles');

-- ─── 2. Employer User ────────────────────────────────────────
-- WARNING: These rows use a static, known password hash ('employer123' / 'seeker123').
-- NEVER run this demo seed file in a production environment. It is strictly for local dev/testing.
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `role`, `is_active`, `is_verified`, `created_at`) VALUES
(10, 'TechCorp HR',   'hr@techcorp.test',    '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01700000001', 'employer', 1, 1, '2026-05-01 10:00:00'),
(11, 'DataWorks Ltd', 'jobs@dataworks.test', '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01700000002', 'employer', 1, 1, '2026-05-01 10:30:00'),
(12, 'GlobalNet Inc', 'hr@globalnet.test',   '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01700000003', 'employer', 1, 1, '2026-05-02 09:00:00');

-- ─── 3. Employer Profiles ────────────────────────────────────
INSERT IGNORE INTO `employer_profiles` (`id`, `user_id`, `company_name`, `industry`, `company_size`, `description`, `website`) VALUES
(1, 10, 'TechCorp',    'Technology', '51-200',  'Leading software solutions company.',   'https://techcorp.test'),
(2, 11, 'DataWorks',   'Finance',    '11-50',   'Data analytics and BI consulting firm.','https://dataworks.test'),
(3, 12, 'GlobalNet',   'Marketing',  '201-500', 'Global digital marketing agency.',      'https://globalnet.test');

-- ─── 4. Jobs ─────────────────────────────────────────────────
INSERT IGNORE INTO `jobs` (`id`, `employer_id`, `category_id`, `title`, `description`, `requirements`, `salary_min`, `salary_max`, `location`, `job_type`, `experience_level`, `deadline`, `status`) VALUES
(1, 10, 1, 'Senior Laravel Developer',    'Build scalable PHP applications.',    'PHP, Laravel, MySQL, 3+ years',    60000, 90000, 'Dhaka',     'full-time', 'senior', '2026-06-30', 'active'),
(2, 10, 1, 'React Frontend Developer',    'Build modern UI components.',         'React, TypeScript, CSS, 2+ years', 50000, 75000, 'Remote',    'remote',    'mid',    '2026-06-30', 'active'),
(3, 11, 2, 'Financial Data Analyst',      'Analyze financial datasets.',         'Excel, SQL, Power BI, 2+ years',   45000, 65000, 'Chittagong','full-time', 'mid',    '2026-07-15', 'active'),
(4, 12, 4, 'Digital Marketing Specialist','Run paid ad campaigns.',              'Google Ads, Meta Ads, SEO, 1+ yr', 35000, 50000, 'Dhaka',     'full-time', 'entry',  '2026-07-01', 'active'),
(5, 11, 5, 'UX Designer',                 'Design user-friendly interfaces.',    'Figma, Adobe XD, User Research',   40000, 60000, 'Remote',    'remote',    'mid',    '2026-06-20', 'active');

-- ─── 5. Applications (from existing seeker users 2 & 3) ──────
INSERT IGNORE INTO `applications` (`id`, `job_id`, `seeker_id`, `cover_letter`, `status`, `applied_at`) VALUES
(1, 1, 2, 'I am very interested in the Laravel Developer role and have 4 years of experience with PHP.',      'submitted',   '2026-05-10 08:30:00'),
(2, 2, 2, 'React is my primary stack. I have built several production-grade SPAs.',                          'reviewed',    '2026-05-11 10:00:00'),
(3, 4, 2, 'I have managed Google Ads campaigns with monthly budgets exceeding $20,000.',                     'shortlisted', '2026-05-12 14:00:00'),
(4, 3, 3, 'I am a detail-oriented analyst with 3 years of experience in financial reporting.',               'submitted',   '2026-05-13 09:15:00'),
(5, 5, 3, 'I design clean, accessible UX with Figma. Attached my portfolio.',                               'interview',   '2026-05-14 11:30:00'),
(6, 1, 3, 'Strong backend developer looking for senior opportunities in a growing team.',                    'rejected',    '2026-05-15 16:00:00');

-- ─── 5.5. Seekers and Seeker Profiles ────────────────────────
-- Password for new seekers: seeker123
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `role`, `is_active`, `is_verified`, `created_at`) VALUES
(13, 'John Doe',        'john@example.com',  '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01711111111', 'seeker', 1, 1, '2026-05-01 10:00:00'),
(14, 'Sarah Connor',    'sarah@example.com', '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01722222222', 'seeker', 1, 1, '2026-05-02 10:00:00'),
(15, 'Michael Scott',   'mike@example.com',  '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01733333333', 'seeker', 1, 1, '2026-05-03 10:00:00'),
(16, 'Emily Chen',      'emily@example.com', '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01744444444', 'seeker', 1, 1, '2026-05-04 10:00:00'),
(17, 'David Miller',    'david@example.com', '$2y$10$AgnIL3.Qr4m41nA4ZSWyUeASlrCMl0E5HcQpCPvVMmbf2dKZBWUxe', '01755555555', 'seeker', 1, 1, '2026-05-05 10:00:00');

INSERT IGNORE INTO `seeker_profiles` (`user_id`, `headline`, `summary`, `skills`, `years_experience`, `education_level`, `expected_salary`, `preferred_location`) VALUES
(2,  'Software Engineer',            'Passionate backend developer.',                         'PHP, Laravel, MySQL',           4, 'Bachelor', 80000, 'Dhaka'),
(3,  'Frontend Developer',           'Creative UI/UX builder.',                               'React, CSS, JS, HTML',          2, 'Bachelor', 50000, 'Remote'),
(13, 'Senior Full-Stack Developer',  'Experienced in scalable web applications.',             'PHP, Vue.js, AWS, Node.js',     6, 'Master',   120000, 'Dhaka'),
(14, 'Data Analyst / Scientist',     'Solving business problems with data.',                  'Python, SQL, Power BI, Excel',  3, 'Bachelor', 60000, 'Chittagong'),
(15, 'Digital Marketing Manager',    'Growing brands through data-driven campaigns.',         'SEO, SEM, Google Ads, Meta',    5, 'Master',   70000, 'Dhaka'),
(16, 'UI/UX Designer',               'Designing human-centric digital experiences.',          'Figma, Adobe XD, Prototyping',  4, 'Bachelor', 55000, 'Remote'),
(17, 'Junior Web Developer',         'Recent graduate eager to learn and grow.',              'HTML, CSS, JavaScript, PHP',    1, 'Bachelor', 30000, 'Dhaka');

-- ─── 6. Fix AUTO_INCREMENT ───────────────────────────────────
ALTER TABLE `users`             AUTO_INCREMENT = 30;
ALTER TABLE `employer_profiles` AUTO_INCREMENT = 10;
ALTER TABLE `jobs`              AUTO_INCREMENT = 10;
ALTER TABLE `applications`      AUTO_INCREMENT = 10;
ALTER TABLE `categories`        AUTO_INCREMENT = 10;
