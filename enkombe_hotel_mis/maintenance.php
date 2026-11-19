<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireRole(['Hotel Manager', 'Maintenance Supervisor']);

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_request'])) {
        $location = trim($_POST['location_name']);
        $issue    = trim($_POST['issue_description']);
        $priority = $_POST['priority_level'];

        if ($location !== '' && $issue !== '') {
            $assignedStaff = (int)$_POST['assigned_staff'];

$stmt = $pdo->prepare("
    INSERT INTO maintenance_requests
    (
        location_name,
        issue_description,
        priority_level,
        status,
        report_date,
        assigned_staff
    )
    VALUES (?, ?, ?, 'Assigned', CURDATE(), ?)
");

$stmt->execute([
    $location,
    $issue,
    $priority,
    $assignedStaff
]);
            $pdo->prepare("UPDATE accommodation_units SET status = 'Maintenance' WHERE unit_name = ?")->execute([$location]);
            $msg = "Maintenance request logged for {$location}.";
        } else {
            $err = "Location and issue description are required.";
        }
    } elseif (isset($_POST['update_request'])) {
        $reqId     = (int)$_POST['request_id'];
        $newStatus = $_POST['status'];
        $location  = $_POST['location_name'];

        $pdo->prepare("UPDATE maintenance_requests SET status = ? WHERE request_id = ?")->execute([$newStatus, $reqId]);
        if ($newStatus === 'Completed') {
            $pdo->prepare("UPDATE accommodation_units SET status = 'Ready' WHERE unit_name = ?")->execute([$location]);
        }
        $msg = "Request #{$reqId} updated to {$newStatus}.";
    }
}

$units = $pdo->query("SELECT unit_name FROM accommodation_units ORDER BY unit_name ASC")->fetchAll(PDO::FETCH_COLUMN);
$technicians = $pdo->query("
    SELECT staff_id, fullname
    FROM staff
    WHERE department IN ('Maintenance', 'Technician','Electrician')
       OR role IN ('Technician', 'Electrician')
    ORDER BY fullname
")->fetchAll();
$requests = $pdo->query("SELECT * FROM maintenance_requests ORDER BY FIELD(status, 'Open', 'In Progress', 'Assigned', 'Completed'), report_date DESC")->fetchAll();

renderHeader("Maintenance Register");
?>

<?php if ($msg): ?>
    <div style="background:#dcfce7; color:#166534; padding:12px 16px; border-radius:6px; margin-bottom:16px; border:1px solid #86efac;">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if ($err): ?>
    <div style="background:#fee2e2; color:#991b1b; padding:12px 16px; border-radius:6px; margin-bottom:16px; border:1px solid #fca5a5;">
        <?= htmlspecialchars($err) ?>
    </div>
<?php endif; ?>

<div style="background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:24px;">
    <h3 style="margin-bottom:14px; font-size:16px; color:#1f2937;">Log New Maintenance Request</h3>
    <form method="POST" style="display:grid; grid-template-columns: 1fr 2fr 1fr 1fr 1fr; gap:12px; align-items:end;">
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Location / Room</label>
            <select name="location_name" required style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
                <?php foreach ($units as $uName): ?>
                    <option value="<?= htmlspecialchars($uName) ?>"><?= htmlspecialchars($uName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">
        Assign Technician
    </label>

    <select name="assigned_staff" required
        style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">

        <?php foreach ($technicians as $tech): ?>

            <option value="<?= $tech['staff_id'] ?>">
                <?= htmlspecialchars($tech['fullname']) ?>
            </option>

        <?php endforeach; ?>

    </select>
</div>
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Priority</label>
            <select name="priority_level" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>
        </div>
        <div>
            <button type="submit" name="add_request" style="width:100%; padding:9px 14px; background:#c59b27; color:#ffffff; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                + Log Fault
            </button>
        </div>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Location</th>
            <th>Issue Description</th>
            <th>Priority</th>
            <th>Report Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td>#<?= htmlspecialchars($r['request_id']) ?></td>
                <td><strong><?= htmlspecialchars($r['location_name']) ?></strong></td>
                <td><?= htmlspecialchars($r['issue_description']) ?></td>
                <td><?= htmlspecialchars($r['priority_level']) ?></td>
                <td><?= htmlspecialchars($r['report_date']) ?></td>
                <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                <td>
                    <form method="POST" style="display:flex; gap:6px;">
                        <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
                        <input type="hidden" name="location_name" value="<?= htmlspecialchars($r['location_name']) ?>">
                        <select name="status" style="padding:4px 8px; border:1px solid #d1d5db; border-radius:4px;">
                            <option value="Open" <?= $r['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                            <option value="In Progress" <?= $r['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $r['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_request" style="padding:4px 10px; background:#1e2d3b; color:#fff; border:none; border-radius:4px; cursor:pointer;">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (function_exists('renderFooter')) renderFooter(); ?>