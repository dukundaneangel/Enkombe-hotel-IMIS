<?php
require_once 'config.php';
require_once 'includes.php';

requireRole([
    'Room Attendant',
    'Technician',
    'Electrician',
    'Operations Officer'
]);

$staff_id = (int)($_SESSION['staff_id'] ?? 0);

if ($staff_id <= 0) {
    die("Your login account is not linked to a staff record. Contact the Hotel Manager.");
}

$msg = '';
$err = '';

// PROCESS EMPLOYEE COMPLETION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $notes = trim($_POST['completion_notes'] ?? '');

    // COMPLETE CLEANING TASK
    if (isset($_POST['complete_cleaning'])) {

        $task_id = (int)$_POST['task_id'];

        $stmt = $pdo->prepare("
            UPDATE housekeeping_tasks
            SET task_status = 'Completed',
                completion_notes = ?,
                inspection_status = 'Not Inspected'
            WHERE task_id = ?
            AND assigned_staff = ?
            AND task_status IN ('Pending', 'In Progress')
        ");

        $stmt->execute([$notes, $task_id, $staff_id]);

        if ($stmt->rowCount() > 0) {
            $msg = "Cleaning task submitted for inspection.";
        } else {
            $err = "Task could not be updated. Check your assigned tasks.";
        }
    }

    // COMPLETE MAINTENANCE TASK
    if (isset($_POST['complete_maintenance'])) {

        $request_id = (int)$_POST['request_id'];

        $stmt = $pdo->prepare("
            UPDATE maintenance_requests
            SET status = 'Completed',
                completion_notes = ?,
                inspection_status = 'Not Inspected'
            WHERE request_id = ?
            AND assigned_staff = ?
            AND status IN ('Open', 'Assigned', 'In Progress')
        ");

        $stmt->execute([$notes, $request_id, $staff_id]);

        if ($stmt->rowCount() > 0) {
            $msg = "Maintenance work submitted for inspection.";
        } else {
            $err = "Maintenance request could not be updated.";
        }
    }
}

// GET EMPLOYEE INFORMATION
$stmt = $pdo->prepare("
    SELECT fullname, department, role
    FROM staff
    WHERE staff_id = ?
");

$stmt->execute([$staff_id]);

$employee = $stmt->fetch();

if (!$employee) {
    die("Employee record not found.");
}

// GET EMPLOYEE CLEANING TASKS
$stmt = $pdo->prepare("
    SELECT *
    FROM housekeeping_tasks
    WHERE assigned_staff = ?
    ORDER BY date_assigned DESC
");

$stmt->execute([$staff_id]);

$cleaningTasks = $stmt->fetchAll();

// GET EMPLOYEE MAINTENANCE JOBS
$stmt = $pdo->prepare("
    SELECT *
    FROM maintenance_requests
    WHERE assigned_staff = ?
    ORDER BY report_date DESC
");

$stmt->execute([$staff_id]);

$maintenanceTasks = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Employee Dashboard | Enkombe Place</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="app-container">

<main class="main-content">

<div class="panel">

    <h1>Employee Dashboard</h1>

    <h2>
        Welcome,
        <?= htmlspecialchars($employee['fullname']) ?>
    </h2>

    <p>
        Department:
        <?= htmlspecialchars($employee['department']) ?>
    </p>

    <p>
        Job Title:
        <?= htmlspecialchars($employee['role']) ?>
    </p>

    <a href="logout.php" class="btn">
        Sign Out
    </a>

</div>


<?php if ($msg): ?>

<div class="alert alert-success">
    <?= htmlspecialchars($msg) ?>
</div>

<?php endif; ?>


<?php if ($err): ?>

<div class="alert alert-error">
    <?= htmlspecialchars($err) ?>
</div>

<?php endif; ?>


<?php if (in_array(
    $_SESSION['role'],
    ['Room Attendant'],
    true
)): ?>

<div class="panel">

<h2>My Cleaning Tasks</h2>

<table>

<thead>

<tr>
    <th>Room</th>
    <th>Date Assigned</th>
    <th>Status</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php foreach ($cleaningTasks as $task): ?>

<tr>

<td>
    <?= htmlspecialchars($task['room_number']) ?>
</td>

<td>
    <?= htmlspecialchars($task['date_assigned']) ?>
</td>

<td>
    <?= htmlspecialchars($task['task_status']) ?>
</td>

<td>

<?php if (in_array(
    $task['task_status'],
    ['Pending', 'In Progress'],
    true
)): ?>

<form method="POST">

<input type="hidden"
       name="task_id"
       value="<?= $task['task_id'] ?>">

<textarea
    name="completion_notes"
    placeholder="Enter completion notes"
    required
></textarea>

<button
    type="submit"
    name="complete_cleaning"
    class="btn"
>
    Mark as Done
</button>

</form>

<?php elseif ($task['task_status'] === 'Completed'): ?>

Waiting for inspection

<?php else: ?>

<?= htmlspecialchars($task['task_status']) ?>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>


<?php if (in_array(
    $_SESSION['role'],
    ['Technician', 'Electrician'],
    true
)): ?>

<div class="panel">

<h2>My Maintenance Jobs</h2>

<table>

<thead>

<tr>
    <th>Location</th>
    <th>Problem</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php foreach ($maintenanceTasks as $task): ?>

<tr>

<td>
    <?= htmlspecialchars($task['location_name']) ?>
</td>

<td>
    <?= htmlspecialchars($task['issue_description']) ?>
</td>

<td>
    <?= htmlspecialchars($task['priority_level']) ?>
</td>

<td>
    <?= htmlspecialchars($task['status']) ?>
</td>

<td>

<?php if (in_array(
    $task['status'],
    ['Open', 'Assigned', 'In Progress'],
    true
)): ?>

<form method="POST">

<input type="hidden"
       name="request_id"
       value="<?= $task['request_id'] ?>">

<textarea
    name="completion_notes"
    placeholder="Describe work completed"
    required
></textarea>

<button
    type="submit"
    name="complete_maintenance"
    class="btn"
>
    Mark as Done
</button>

</form>

<?php elseif ($task['status'] === 'Completed'): ?>

Waiting for inspection

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</main>

</div>

</body>

</html>