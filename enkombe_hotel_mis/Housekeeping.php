<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireRole(['Hotel Manager', 'Housekeeping Supervisor']);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_task'])) {
        $bedroomId = (int)$_POST['bedroom_id'];
        $uStmt = $pdo->prepare("SELECT unit_name FROM accommodation_units WHERE unit_id = ?");
        $uStmt->execute([$bedroomId]);
        $roomNumber = $uStmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO housekeeping_tasks (room_number, assigned_staff, task_status, date_assigned) VALUES (?, ?, 'Pending', ?)");
        $stmt->execute([$roomNumber, (int)$_POST['assigned_staff_id'], $_POST['task_date']]);

        $pdo->prepare("UPDATE accommodation_units SET status = 'Needs Cleaning' WHERE unit_id = ?")->execute([$bedroomId]);
        $msg = "Cleaning task created for {$roomNumber} and marked 'Needs Cleaning'.";
    } elseif (isset($_POST['complete_task'])) {
        $taskId = (int)$_POST['task_id'];
        $bedroomId = (int)$_POST['bedroom_id'];

        $pdo->prepare("UPDATE housekeeping_tasks SET task_status = 'Completed' WHERE task_id = ?")->execute([$taskId]);
        $pdo->prepare("UPDATE accommodation_units SET status = 'Ready' WHERE unit_id = ?")->execute([$bedroomId]);
        $msg = "Cleaning marked complete. Room is now Ready.";
    }
}

$bedrooms = $pdo->query("SELECT unit_id AS bedroom_id, unit_name AS bedroom_no, unit_name FROM accommodation_units ORDER BY unit_name")->fetchAll();
$hkStaff = $pdo->query("
    SELECT staff_id, fullname AS full_name
    FROM staff
    WHERE department IN ('Housekeeping','Room Attendant')
       OR role IN ('Room Attendant','Attendant')
    ORDER BY fullname
")->fetchAll();

$tasks = $pdo->query("
    SELECT t.*, t.room_number AS bedroom_no, u.unit_id AS bedroom_id, u.unit_name, s.fullname AS staff_name, t.date_assigned AS task_date, '' AS instructions
    FROM housekeeping_tasks t
    LEFT JOIN accommodation_units u ON t.room_number = u.unit_name
    LEFT JOIN staff s ON t.assigned_staff = s.staff_id
    ORDER BY FIELD(t.task_status, 'Reopened', 'Pending', 'In Progress', 'Completed'), t.date_assigned DESC
")->fetchAll();

renderHeader("Housekeeping Tasks");
?>

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="two-col">
    <div class="panel">
        <h2>Assign Cleaning Task</h2>
        <form method="POST">
            <div class="form-group">
                <label>Target Bedroom</label>
                <select name="bedroom_id" required>
                    <?php foreach ($bedrooms as $b): ?>
                        <option value="<?= $b['bedroom_id'] ?>"><?= htmlspecialchars($b['unit_name'] . ' - ' . $b['bedroom_no']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Assign Housekeeping Staff</label>
                <select name="assigned_staff_id" required>
                    <?php foreach ($hkStaff as $st): ?>
                        <option value="<?= $st['staff_id'] ?>"><?= htmlspecialchars($st['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Task Date</label>
                <input type="date" name="task_date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Cleaning Instructions</label>
                <textarea name="instructions" rows="3" placeholder="e.g. Turnover clean, sanitize bathroom, change towels"></textarea>
            </div>
            <button type="submit" name="create_task" class="btn">Assign Task</button>
        </form>
    </div>

    <div class="panel">
        <h2>Housekeeping Task Queue</h2>
        <table>
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $t): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($t['unit_name']) ?></strong><br><small><?= htmlspecialchars($t['bedroom_no']) ?></small></td>
                    <td><?= htmlspecialchars($t['staff_name']) ?></td>
                    <td><?= htmlspecialchars($t['task_date']) ?></td>
                    <td>
                        <span class="badge <?= $t['task_status'] === 'Completed' ? 'badge-ready' : ($t['task_status'] === 'Reopened' ? 'badge-oos' : 'badge-dirty') ?>">
                            <?= htmlspecialchars($t['task_status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($t['task_status'] !== 'Completed'): ?>
                        <form method="POST" style="display:flex; gap:0.4rem;">
                            <input type="hidden" name="task_id" value="<?= $t['task_id'] ?>">
                            <input type="hidden" name="bedroom_id" value="<?= $t['bedroom_id'] ?>">
                            <input type="text" name="completion_notes" placeholder="Notes..." style="padding:0.3rem; font-size:0.8rem; width:110px;">
                            <button type="submit" name="complete_task" class="btn btn-sm btn-success">Mark Done</button>
                        </form>
                        <?php else: ?>
                            <small style="color:#6b7280;">Sent to Inspection</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php renderFooter(); ?>


