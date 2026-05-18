<?php
// config/app.php — Auto-detects the base URL from the server environment.
// Works on ANY machine, ANY folder name, ANY XAMPP setup.
// Include this ONCE at the top of index.php. Never hardcode paths again.

if (!defined('APP_ROOT')) {
    // Absolute filesystem path to this project's root (where index.php lives)
    define('APP_ROOT', dirname(__DIR__));

    // Build the web base URL dynamically:
    // e.g.  http://localhost/job_portal/seeker
    //       http://localhost/seeker
    //       http://localhost/myproject
    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // document_root e.g. C:/xampp/htdocs  or  /var/www/html
    $docRoot  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');

    // APP_ROOT e.g. C:/xampp/htdocs/job_portal/seeker
    $appRoot  = rtrim(str_replace('\\', '/', APP_ROOT), '/');

    // Web path = APP_ROOT minus DOCUMENT_ROOT
    // e.g.  /job_portal/seeker
    $webPath  = str_replace($docRoot, '', $appRoot);

    // BASE_URL = full URL prefix with no trailing slash
    // e.g.  http://localhost/job_portal/seeker
    define('BASE_URL', $scheme . '://' . $host . $webPath);

    // BASE_PATH = web path only, no trailing slash
    // e.g.  /job_portal/seeker
    define('BASE_PATH', $webPath);
}
