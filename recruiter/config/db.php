<?php
// Recruiter Module: Database Connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";           // CHANGE LOCALLY IF NEEDED, BUT KEEP EMPTY ON GITHUB
$db_name = "job_portal_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
$conn->set_charset("utf8mb4");
?>
