<?php
require_once 'config.php';
require_once 'includes.php';
require_once 'layout.php';

requireRole(['Hotel Manager']);

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD A STAFF MEMBER
    if (isset($_POST['add_staff'])) {

        $name = trim($_POST['full_name']);
        $department = trim($_POST['department']);
        $job = trim($_POST['job_title']);

        $stmt = $pdo->prepare(
            "INSERT INTO staff (fullname, department, role)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$name, $department, $job]);

        $msg = "Staff member added successfully.";
    }

    // CREATE EMPLOYEE LOGIN ACCOUNT
    if (isset($_POST['create_account'])) {

        $staff_id = (int)$_POST['staff_id'];
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        $allowedRoles = [
            'Hotel Manager',
            'Housekeeping Supervisor',
            'Maintenance Supervisor',
            'Room Attendant',
            'Technician',
            'Electrician',
            'Operations Officer'
        ];

        if (!in_array($role, $allowedRoles, true)) {

            $err = "Invalid system role.";

        } elseif (strlen($password) < 8) {

            $err = "Password must have at least 8 characters.";

        } else {

            try {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (staff_id, username, password, role)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $staff_id,
                    $username,
                    $hashedPassword,
                    $role
                ]);

                $msg = "Employee login account created successfully.";

            } catch (PDOException $e) {

                $err = "Account could not be created. Check that the employee does not already have an account and the username is not already used.";

            }
        }
    }
}

// GET STAFF WHO DO NOT HAVE LOGIN ACCOUNTS
$staffWithoutAccounts = $pdo->query("
    SELECT s.staff_id, s.fullname, s.department, s.role
    FROM staff s
    LEFT JOIN users u ON s.staff_id = u.staff_id
    WHERE u.user_id IS NULL
    ORDER BY s.fullname
")->fetchAll();

// GET ALL STAFF AND THEIR LOGIN INFORMATION
$staffList = $pdo->query("
    SELECT s.staff_id,
           s.fullname,
           s.department,
           s.role AS job_title,
           u.username,
           u.role AS system_role
    FROM staff s
    LEFT JOIN users u ON s.staff_id = u.staff_id
    ORDER BY s.department, s.fullname
")->fetchAll();

renderHeader("Staff & Departments");
?>

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

<div class="two-col">

    <div>

        <div class="panel">
            <h2>Add Operational Staff</h2>

            <form method="POST">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>

                <div class="form-group">
                    <label>Department</label>

                    <select name="department" required>
                        <option value="Management">Management</option>
                        <option value="Housekeeping">Housekeeping</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Operations">Operations</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Job Title</label>

                    <input
                        type="text"
                        name="job_title"
                        placeholder="e.g. Room Attendant"
                        required
                    >
                </div>

                <button type="submit" name="add_staff" class="btn">
                    Save Staff Record
                </button>

            </form>
        </div>


        <div class="panel">

            <h2>Create Employee Login Account</h2>

            <form method="POST">

                <div class="form-group">
                    <label>Select Employee</label>

                    <select name="staff_id" required>

                        <?php foreach ($staffWithoutAccounts as $s): ?>

                        <option value="<?= $s['staff_id'] ?>">
                            <?= htmlspecialchars($s['fullname']) ?>
                            -
                            <?= htmlspecialchars($s['role']) ?>
                        </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>Username</label>

                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password (minimum 8 characters)</label>

                    <input type="password" name="password"
                           minlength="8" required>
                </div>

                <div class="form-group">
                    <label>System Role</label>

                    <select name="role" required>

                        <option value="Room Attendant">
                            Room Attendant
                        </option>

                        <option value="Technician">
                            Technician
                        </option>

                        <option value="Electrician">
                            Electrician
                        </option>

                        <option value="Operations Officer">
                            Operations Officer
                        </option>

                        <option value="Housekeeping Supervisor">
                            Housekeeping Supervisor
                        </option>

                        <option value="Maintenance Supervisor">
                            Maintenance Supervisor
                        </option>

                        <option value="Hotel Manager">
                            Hotel Manager
                        </option>

                    </select>
                </div>

                <button type="submit" name="create_account" class="btn">
                    Create Employee Account
                </button>

            </form>

        </div>

    </div>


    <div class="panel">

        <h2>Hotel Staff Directory</h2>

        <table>

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Job Title</th>
                    <th>System Access</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($staffList as $s): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($s['fullname']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($s['department']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($s['job_title'] ?? '') ?>
                    </td>

                    <td>

                        <?php if ($s['username']): ?>

                            <?= htmlspecialchars($s['system_role']) ?>
                            (<?= htmlspecialchars($s['username']) ?>)

                        <?php else: ?>

                            No Login Account

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php renderFooter(); ?>