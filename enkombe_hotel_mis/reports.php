<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireLogin();

$statusFilter = $_GET['room_status'] ?? '';

$sql = "SELECT u.unit_name AS bedroom_no, u.status AS room_status, u.unit_name, u.unit_type
        FROM accommodation_units u";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE u.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY u.unit_name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$roomReport = $stmt->fetchAll();

$failedInspections = $pdo->query("
    SELECT t.date_assigned AS inspection_date, 'Reopened for cleaning' AS comments, t.room_number AS bedroom_no,
           COALESCE(u.unit_name, t.room_number) AS unit_name, COALESCE(s.fullname, 'Unassigned') AS cleaner
    FROM housekeeping_tasks t
    LEFT JOIN accommodation_units u ON t.room_number = u.unit_name
    LEFT JOIN staff s ON t.assigned_staff = s.staff_id
    WHERE t.task_status = 'Reopened'
    ORDER BY t.date_assigned DESC
")->fetchAll();

$maintSummary = $pdo->query("
    SELECT m.status, m.priority_level AS priority, m.issue_description AS issue,
           m.report_date AS opened_date, NULL AS closed_date,
           CONCAT(m.location_name, ' (Unit)') AS target
    FROM maintenance_requests m
    ORDER BY m.report_date DESC
")->fetchAll();

renderHeader("Management & Operational Reports");
?>

<div class="panel">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2>1. Room Readiness Report</h2>
        <form method="GET" style="display:flex; gap:0.5rem;">
            <select name="room_status" style="padding:0.4rem;">
                <option value="">All Statuses</option>
               <?php foreach (['Ready', 'Occupied', 'Needs Cleaning', 'Maintenance'] as $st): ?>
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




