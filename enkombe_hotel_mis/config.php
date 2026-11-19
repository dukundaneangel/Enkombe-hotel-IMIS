<?php
$host = 'localhost';
$dbname = 'enkombe_hotel_mis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

function logAudit(PDO $pdo, ?int $userId, string $action, string $details): void {
    $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $action, $details]);
}