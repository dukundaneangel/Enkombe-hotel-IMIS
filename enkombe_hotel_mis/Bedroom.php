<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';
requireLogin();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_bedroom']) && hasRole(['Manager', 'Hotel Manager'])) {
        $stmt = $pdo->prepare("INSERT INTO accommodation_units (unit_name, unit_type, status) VALUES (?, 'Suite', ?)");
        $stmt->execute([trim($_POST['bedroom_no']), $_POST['room_status']]);
        $msg = "Bedroom added successfully.";
    } elseif (isset($_POST['update_status']) && hasRole(['Manager', 'Hotel Manager', 'Housekeeping Supervisor'])) {
        $bedroomId = (int)$_POST['bedroom_id'];
        $newStatus = $_POST['room_status'];

        $unitStmt = $pdo->prepare("SELECT unit_name FROM accommodation_units WHERE unit_id = ?");
        $unitStmt->execute([$bedroomId]);
        $unitName = $unitStmt->fetchColumn();

        $faultStmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_requests WHERE location_name = ? AND status != 'Completed'");
        $faultStmt->execute([$unitName]);
        $openFaults = $faultStmt->fetchColumn();

        if (($newStatus === 'Ready' || $newStatus === 'Clean / ready') && $openFaults > 0) {
            $err = "Blocked: Cannot mark bedroom {$unitName} as Ready while open maintenance faults exist.";
        } else {
            $stmt = $pdo->prepare("UPDATE accommodation_units SET status = ? WHERE unit_id = ?");
            $stmt->execute([$newStatus, $bedroomId]);
            $msg = "Bedroom {$unitName} status updated to {$newStatus}.";
        }
    }
}

$units = $pdo->query("SELECT unit_id, unit_name FROM accommodation_units ORDER BY unit_name")->fetchAll();
$bedrooms = $pdo->query("
    SELECT u.unit_id AS bedroom_id, u.unit_name AS bedroom_no, u.status AS room_status, u.unit_name,
           (SELECT COUNT(*) FROM maintenance_requests m WHERE m.location_name = u.unit_name AND m.status != 'Completed') AS open_faults
    FROM accommodation_units u
    ORDER BY u.unit_name
")->fetchAll();

renderHeader("Bedroom Status Board");
?>

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="<?= hasRole(['Hotel Manager']) ? 'two-col' : '' ?>">
    <?php if (hasRole(['Hotel Manager'])): ?>
    <div class="panel">
        <h2>Add Bedroom to Unit</h2>
        <form method="POST">
            <div class="form-group">
                <label>Parent Accommodation Unit</label>
                <select name="unit_id" required>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Bedroom Identifier</label>
                <input type="text" name="bedroom_no" placeholder="e.g. 101-A (Master)" required>
            </div>
            <div class="form-group">
                <label>Initial Status</label>
                <select name="room_status">
                    <option value="Vacant - needs cleaning">Vacant - needs cleaning</option>
                    <option value="Clean / ready">Clean / ready</option>
                    <option value="Occupied">Occupied</option>
                    <option value="Out of service">Out of service</option>
                </select>
            </div>
            <button type="submit" name="add_bedroom" class="btn">Save Bedroom</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Live Bedroom Readiness Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Bedroom</th>
                    <th>Current Status</th>
                    <th>Availability</th>
                    <?php if (hasRole(['Hotel Manager', 'Housekeeping Supervisor'])): ?><th>Quick Status Update</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bedrooms as $b): 
                    $badgeClass = match($b['room_status']) {
                        'Clean / ready' => 'badge-ready',
                        'Out of service' => 'badge-oos',
                        'Occupied' => 'badge-info',
                        default => 'badge-dirty'
                    };
                ?>
                <tr>
                    <td><?= htmlspecialchars($b['unit_name']) ?></td>
                    <td><strong><?= htmlspecialchars($b['bedroom_no']) ?></strong></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($b['room_status']) ?></span></td>
                    <td>
                        <?= ($b['room_status'] === 'Clean / ready' && $b['open_faults'] == 0) 
                            ? '<strong style="color:#15803d;">Ready for Guest</strong>' 
                            : '<span style="color:#b91c1c;">Not Ready</span>' ?>
                    </td>
                    <?php if (hasRole(['Hotel Manager', 'Housekeeping Supervisor'])): ?>
                    <td>
                        <form method="POST" style="display:flex; gap:0.4rem;">
                            <input type="hidden" name="bedroom_id" value="<?= $b['bedroom_id'] ?>">
                            <select name="room_status" style="padding:0.3rem; font-size:0.8rem;">
                                <?php foreach (['Occupied', 'Vacant - needs cleaning', 'Cleaning in progress', 'Awaiting inspection', 'Clean / ready', 'Out of service'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $b['room_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm">Update</button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php renderFooter(); ?>


