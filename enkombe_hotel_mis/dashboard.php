<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireLogin();

$totalUnits = $pdo->query("SELECT COUNT(*) FROM accommodation_units")->fetchColumn();
$totalBedrooms = $pdo->query("SELECT COALESCE(SUM(bedrooms), COUNT(*)) FROM accommodation_units")->fetchColumn();

$statusCounts = $pdo->query("SELECT status, COUNT(*) AS cnt FROM accommodation_units GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

$openCleaning = $pdo->query("SELECT COUNT(*) FROM housekeeping_tasks WHERE task_status IN ('Pending', 'In Progress', 'Reopened')")->fetchColumn();
$openMaint = $pdo->query("SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('Open', 'In Progress')")->fetchColumn();

$recentLogs = $pdo->query("
    (SELECT CONCAT(date_assigned, ' 08:00:00') AS created_at, 'Housekeeping' AS full_name,
            CONCAT('Task ', task_status) AS action_type,
            CONCAT('Room ', room_number, ' cleaning task') AS details
     FROM housekeeping_tasks)
    UNION ALL
    (SELECT CONCAT(report_date, ' 09:00:00') AS created_at, 'Maintenance' AS full_name,
            CONCAT('Fault ', status) AS action_type,
            CONCAT(location_name, ': ', issue_description) AS details
     FROM maintenance_requests)
    ORDER BY created_at DESC
    LIMIT 8
")->fetchAll();

renderHeader("Management Dashboard");
?>

<div class="kpi-grid">
    <div class="kpi-card">
        <h3>Accommodation Units</h3>
        <div class="num"><?= $totalUnits ?></div>
    </div>
    <div class="kpi-card">
        <h3>Total Bedrooms</h3>
        <div class="num"><?= $totalBedrooms ?></div>
    </div>
    <div class="kpi-card" style="border-top-color:#15803d;">
        <h3>Clean / Ready</h3>
        <div class="num"><?= $statusCounts['Ready'] ?? 0 ?></div>
    </div>
    <div class="kpi-card" style="border-top-color:#1d4ed8;">
        <h3>Occupied</h3>
        <div class="num"><?= $statusCounts['Occupied'] ?? 0 ?></div>
    </div>
    <div class="kpi-card" style="border-top-color:#b45309;">
        <h3>Needs Cleaning / Insp.</h3>
        <div class="num"><?= $statusCounts['Needs Cleaning'] ?? 0 ?></div>
    </div>
    <div class="kpi-card" style="border-top-color:#b91c1c;">
        <h3>Out of Service</h3>
        <div class="num"><?= $statusCounts['Maintenance'] ?? 0 ?></div>
    </div>
    <div class="kpi-card">
        <h3>Open Cleaning Tasks</h3>
        <div class="num"><?= $openCleaning ?></div>
    </div>
    <div class="kpi-card">
        <h3>Open Maintenance</h3>
        <div class="num"><?= $openMaint ?></div>
    </div>
</div>

<div class="panel">
    <h2>Recent System Activity (Audit Trail)</h2>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentLogs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['created_at']) ?></td>
                <td><?= htmlspecialchars($log['full_name'] ?? 'System') ?></td>
                <td><span class="badge badge-info"><?= htmlspecialchars($log['action_type']) ?></span></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php renderFooter(); ?>

