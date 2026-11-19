<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}

function hasRole(array $allowedRoles): bool {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    $role = $_SESSION['role'];
    if ($role === 'Manager' && in_array('Hotel Manager', $allowedRoles, true)) {
        return true;
    }
    return in_array($role, $allowedRoles, true);
}

function requireRole(array $allowedRoles): void {
    requireLogin();
    if (!hasRole($allowedRoles)) {
        http_response_code(403);
        $roleSafe = htmlspecialchars($_SESSION['role'] ?? 'Unknown');
        die("<h2>Access Denied</h2><p>Your role ({$roleSafe}) does not have permission to access this module.</p><a href='dashboard.php'>Return to Dashboard</a>");
    }
}