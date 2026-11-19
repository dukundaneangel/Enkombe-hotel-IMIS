<?php
require_once 'config.php';
session_start();
if (isset($_SESSION['user_id'])) {
    logAudit($pdo, $_SESSION['user_id'], 'LOGOUT', "User logged out.");
}
session_destroy();
header("Location: index.php");
exit;