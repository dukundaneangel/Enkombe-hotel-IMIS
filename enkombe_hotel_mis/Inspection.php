<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireRole(['Hotel Manager', 'Housekeeping Supervisor']);

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_inspection'])) {
    $taskId    = (int)$_POST['task_id'];
    $bedroomId = (int)$_POST['bedroom_id'];
    $result    = $_POST['result'];
    $comments  = trim($_POST['comments']);

    $uStmt = $pdo->prepare("SELECT unit_name FROM accommodation_units WHERE unit_id = ?");
    $uStmt->execute([$bedroomId]);
    $unitName = $uStmt->fetchColumn();

    $faultStmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_requests WHERE location_name = ? AND status != 'Completed'");
    $faultStmt->execute([$unitName]);
    $openFaults = $faultStmt->fetchColumn();

    if ($result === 'Pass' && $openFaults > 0) {
        $err = "Cannot pass {$unitName} as Ready because an open maintenance fault exists on this room.";
    } else {
       if ($result === 'Pass') {

    $pdo->prepare("
        UPDATE housekeeping_tasks
        SET inspection_status = 'Approved'
        WHERE task_id = ?
    ")->execute([$taskId]);

    $pdo->prepare("
        UPDATE accommodation_units
        SET status = 'Ready'
        WHERE unit_id = ?
    ")->execute([$bedroomId]);

    $msg = "Inspection PASSED. Room {$unitName} is now Clean / Ready.";

} else {

    $pdo->prepare("
        UPDATE housekeeping_tasks
        SET task_status = 'Reopened',
            inspection_status = 'Rejected'
        WHERE task_id = ?
    ")->execute([$taskId]);

    $pdo->prepare("
        UPDATE accommodation_units
        SET status = 'Needs Cleaning'
        WHERE unit_id = ?
    ")->execute([$bedroomId]);

    $msg = "Inspection FAILED. Task reopened for correction.";

}
    }
}

$awaitingTasks = $pdo->query("
    SELECT t.task_id, u.unit_id AS bedroom_id, u.unit_name AS bedroom_no, u.unit_name, COALESCE(s.fullname, 'Unassigned') AS cleaner
    FROM housekeeping_tasks t
    JOIN accommodation_units u ON t.room_number = u.unit_name
    LEFT JOIN staff s ON t.assigned_staff = s.staff_id
    WHERE t.task_status = 'Completed'
    ORDER BY t.date_assigned DESC
")->fetchAll();
$history = [];
renderHeader("Room Inspections");
?>

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="panel">
    <h2>Bedrooms Awaiting Inspection</h2>
    <?php if (empty($awaitingTasks)): ?>
        <p style="color:#6b7280;">No bedrooms are currently awaiting inspection.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Unit & Bedroom</th>
                <th>Cleaned By</th>
                <th>Inspection Decision & Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($awaitingTasks as $at): ?>
            <tr>
                <td><strong><?= htmlspecialchars($at['unit_name']) ?></strong> - <?= htmlspecialchars($at['bedroom_no']) ?></td>
                <td><?= htmlspecialchars($at['cleaner']) ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:0.5rem; align-items:center;">
                        <input type="hidden" name="task_id" value="<?= $at['task_id'] ?>">
                        <input type="hidden" name="bedroom_id" value="<?= $at['bedroom_id'] ?>">
                        <select name="result" style="padding:0.4rem;">
                            <option value="Pass">Pass (Mark Ready)</option>
                            <option value="Fail">Fail (Reopen Task)</option>
                        </select>
                        <input type="text" name="comments" placeholder="Inspection remarks..." required style="padding:0.4rem; flex:1;">
                        <button type="submit" name="record_inspection" class="btn btn-sm">Submit</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="panel">
    <h2>Recent Inspection Log</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Room</th>
                <th>Inspector</th>
                <th>Result</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $h): ?>
            <tr>
                <td><?= htmlspecialchars($h['inspection_date']) ?></td>
                <td><?= htmlspecialchars($h['unit_name'] . ' - ' . $h['bedroom_no']) ?></td>
                <td><?= htmlspecialchars($h['inspector']) ?></td>
                <td><span class="badge <?= $h['result'] === 'Pass' ? 'badge-ready' : 'badge-fail' ?>"><?= $h['result'] ?></span></td>
                <td><?= htmlspecialchars($h['comments']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php renderFooter(); ?>


