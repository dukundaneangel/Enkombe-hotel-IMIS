<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireLogin();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit']) && hasRole(['Manager', 'Hotel Manager'])) {
    $unitName = trim($_POST['unit_name']);
    $unitType = trim($_POST['unit_type']);
    $bedrooms = (int)$_POST['bedrooms'];
    $status   = $_POST['status'];

    if ($unitName !== '') {
        $stmt = $pdo->prepare("INSERT INTO accommodation_units (unit_name, unit_type, bedrooms, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$unitName, $unitType, $bedrooms, $status]);
        $msg = "Accommodation unit '{$unitName}' added successfully.";
    } else {
        $err = "Unit name is required.";
    }
}

$units = $pdo->query("SELECT * FROM accommodation_units ORDER BY unit_name ASC")->fetchAll();

renderHeader("Accommodation Units");
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

<?php if (hasRole(['Manager', 'Hotel Manager'])): ?>
<div style="background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:24px;">
    <h3 style="margin-bottom:14px; font-size:16px; color:#1f2937;">Add New Accommodation Unit</h3>
    <form method="POST" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:12px; align-items:end;">
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Unit Name</label>
            <input type="text" name="unit_name" placeholder="e.g. A06" required style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
        </div>
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Unit Type</label>
            <select name="unit_type" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="Luxury Suite">Luxury Suite</option>
                <option value="Executive Apartment">Executive Apartment</option>
                <option value="Family Apartment">Family Apartment</option>
                <option value="Standard Room">Standard Room</option>
            </select>
        </div>
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Bedrooms</label>
            <input type="number" name="bedrooms" value="1" min="1" max="10" required style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
        </div>
        <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Status</label>
            <select name="status" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="Ready">Ready</option>
                <option value="Occupied">Occupied</option>
                <option value="Needs Cleaning">Needs Cleaning</option>
                <option value="Maintenance">Maintenance</option>
            </select>
        </div>
        <div>
            <button type="submit" name="add_unit" style="width:100%; padding:9px 14px; background:#c59b27; color:#ffffff; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                + Add Unit
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Unit ID</th>
            <th>Unit Name</th>
            <th>Unit Type</th>
            <th>Bedrooms</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($units as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['unit_id']) ?></td>
                <td><strong><?= htmlspecialchars($u['unit_name']) ?></strong></td>
                <td><?= htmlspecialchars($u['unit_type']) ?></td>
                <td><?= htmlspecialchars($u['bedrooms'] ?? '1') ?></td>
                <td>
                    <span style="padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600; background:#f1f5f9; color:#1e293b;">
                        <?= htmlspecialchars($u['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (function_exists('renderFooter')) renderFooter(); ?>