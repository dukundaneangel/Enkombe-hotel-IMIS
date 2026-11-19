<?php
require_once "config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){ header("location: index.php"); exit; }
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])){
    if($_POST['action'] == 'add_task'){
        $room = trim($_POST['room_number']); $staff = intval($_POST['assigned_staff']); $status = trim($_POST['task_status']); $date = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO housekeeping_tasks (room_number, assigned_staff, task_status, date_assigned) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $room, $staff, $status, $date); $stmt->execute(); $stmt->close(); header("location: dashboard.php"); exit;
    }
    if($_POST['action'] == 'add_maintenance'){
        $room = trim($_POST['room_number']); $desc = trim($_POST['issue_description']); $priority = trim($_POST['priority']); $status = 'Open'; $staff = intval($_POST['assigned_staff']);
        $stmt = $conn->prepare("INSERT INTO maintenance_requests (room_number, issue_description, priority, status, assigned_staff) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $room, $desc, $priority, $status, $staff); $stmt->execute(); $stmt->close(); header("location: dashboard.php"); exit;
    }
}
$staff_arr = []; $res_staff = $conn->query("SELECT staff_id, fullname, phone, department, role FROM staff");
if($res_staff) { while($row = $res_staff->fetch_assoc()){ $staff_arr[] = $row; } }
$units_arr = []; $res_units = $conn->query("SELECT unit_id, unit_name, unit_type, bedrooms, status FROM accommodation_units");
if($res_units) { while($row = $res_units->fetch_assoc()){ $units_arr[] = $row; } }
$tasks_arr = []; $res_tasks = $conn->query("SELECT task_id, room_number, assigned_staff, task_status, date_assigned FROM housekeeping_tasks");
if($res_tasks) { while($row = $res_tasks->fetch_assoc()){ $tasks_arr[] = $row; } }
$maint_arr = []; $res_maint = $conn->query("SELECT request_id, room_number, issue_description, priority, status FROM maintenance_requests");
if($res_maint) { while($row = $res_maint->fetch_assoc()){ $maint_arr[] = $row; } }
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enkombe Place Hotel and Suites IMIS - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <h1>Enkombe Place Hotel and Suites IMIS</h1>
        <div><span style="margin-right: 15px;">Role: <strong><?php echo htmlspecialchars($_SESSION["role"]); ?></strong></span><a href="logout.php" class="logout-btn">Logout</a></div>
    </nav>
    <div class="container">
        <div class="header-area">
            <h2>Internal Operations Workspace</h2>
            <div><button class="btn btn-primary" onclick="openModal('taskModal')">+ Assign Cleaning Task</button><button class="btn btn-danger" onclick="openModal('maintenanceModal')">+ Log Maintenance Fault</button></div>
        </div>