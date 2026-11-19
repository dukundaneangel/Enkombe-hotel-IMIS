<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireLogin();

$statusFilter = $_GET['room_status'] ?? '';

$sql = "SELECT b.bedroom_no, b.room_status, u.unit_name, u.unit_type 
        FROM bedroom b 
        JOIN accommodation_unit u ON b.unit_id = u.unit_id";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE b.room_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY u.unit_name, b.bedroom_no";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$roomReport = $stmt->fetchAll();

$failedInspections = $pdo->query("
    SELECT i.inspection_date, i.comments, b.bedroom_no, u.unit_name, s.full_name AS cleaner
    FROM inspection i
    JOIN housekeeping_task t ON i.task_id = t.task_id
    JOIN bedroom b ON t.bedroom_id = b.bedroom_id
    JOIN accommodation_unit u ON b.unit_id = u.unit_id
    JOIN staff s ON t.assigned_staff_id = s.staff_id
    WHERE i.result = 'Fail'
    ORDER BY i.inspection_date DESC
")->fetchAll();

$maintSummary = $pdo->query("
    SELECT m.status, m.priority, m.issue, m.opened_date, m.closed_date,
           COALESCE(CONCAT(bu.unit_name, ' - ', b.bedroom_no), CONCAT(u.unit_name, ' (Unit)')) AS target
    FROM maintenance_request m
    LEFT JOIN bedroom b ON m.bedroom_id = b.bedroom_id
    LEFT JOIN accommodation_unit bu ON b.unit_id = bu.unit_id
    LEFT JOIN accommodation_unit u ON m.unit_id = u.unit_id
    ORDER BY m.opened_date DESC
")->fetchAll();

renderHeader("Management & Operational Reports");
?>

<div class="panel">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2>1. Room Readiness Report</h2>
        <form method="GET" style="display:flex; gap:0.5rem;">
            <select name="room_status" style="padding:0.4rem;">
                <option value="">All Statuses</option>
                <?php foreach (['Clean / ready', 'Occupied', 'Vacant - needs cleaning', 'Cleaning in progress', 'Awaiting inspection', 'Out of service'] as $st): ?>
                    <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm">Filter</button>
            <button type="button" onclick="window.print()" class="btn btn-sm" style="background:#4b5563;">Print Report</button>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>Accommodation Unit</th>
                <th>Unit Type</th>
                <th>Bedroom</th>
                <th>Operational Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roomReport as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['unit_name']) ?></td>
                <td><?= htmlspecialchars($r['unit_type']) ?></td>
                <td><strong><?= htmlspecialchars($r['bedroom_no']) ?></strong></td>
                <td><?= htmlspecialchars($r['room_status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="panel">
    <h2>2. Failed Housekeeping Inspections Report</h2>
    <?php if (empty($failedInspections)): ?>
        <p style="color:#6b7280;">No failed inspections recorded.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Room</th>
                <th>Assigned Cleaner</th>
                <th>Supervisor Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($failedInspections as $fi): ?>
            <tr>
                <td><?= htmlspecialchars($fi['inspection_date']) ?></td>
                <td><?= htmlspecialchars($fi['unit_name'] . ' - ' . $fi['bedroom_no']) ?></td>
                <td><?= htmlspecialchars($fi['cleaner']) ?></td>
                <td><?= htmlspecialchars($fi['comments']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="panel">
    <h2>3. Maintenance Requests Summary (Open & Completed)</h2>
    <table>
        <thead>
            <tr>
                <th>Location</th>
                <th>Issue</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Opened</th>
                <th>Closed</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintSummary as $ms): ?>
            <tr>
                <td><?= htmlspecialchars($ms['target']) ?></td>
                <td><?= htmlspecialchars($ms['issue']) ?></td>
                <td><?= htmlspecialchars($ms['priority']) ?></td>
                <td><span class="badge <?= $ms['status'] === 'Closed' ? 'badge-ready' : 'badge-dirty' ?>"><?= $ms['status'] ?></span></td>
                <td><?= htmlspecialchars($ms['opened_date']) ?></td>
                <td><?= htmlspecialchars($ms['closed_date'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php renderFooter(); ?>




