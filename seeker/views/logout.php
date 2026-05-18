<?php
// Seeker Module: Logout
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
