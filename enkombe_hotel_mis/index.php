<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ? LIMIT 1"
    );

    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if ($user) {

        $validPassword = password_verify(
            $password,
            $user['password']
        );

        // Upgrade old plain-text demo passwords
        if (
            !$validPassword &&
            $password === $user['password']
        ) {

            $newHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $update = $pdo->prepare(
                "UPDATE users SET password = ? WHERE user_id = ?"
            );

            $update->execute([
                $newHash,
                $user['user_id']
            ]);

            $validPassword = true;
        }

        if ($validPassword) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['role'] = $user['role'];

            $staffName = '';

            if (!empty($user['staff_id'])) {

                $staffStmt = $pdo->prepare(
                    "SELECT fullname FROM staff WHERE staff_id = ?"
                );

                $staffStmt->execute([$user['staff_id']]);

                $staffName = $staffStmt->fetchColumn();
            }

            $_SESSION['full_name'] =
                $staffName ?: ucfirst($user['username']);

            $employeeRoles = [
                'Room Attendant',
                'Technician',
                'Electrician',
                'Operations Officer'
            ];

            if (in_array($user['role'], $employeeRoles, true)) {

                header("Location: employee_dashboard.php");

            } else {

                header("Location: dashboard.php");

            }

            exit;

        } else {

            $error = "Invalid username or password.";

        }

    } else {

        $error = "Invalid username or password.";

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Enkombe Place Hotel IMIS</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h1>Enkombe Place Hotel</h1>
        <p>Internal Management Information System (IMIS)</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn" style="width:100%">Sign In</button>
        </form>
        <div style="margin-top:1.5rem; font-size:0.8rem; color:#6b7280; border-top:1px solid #e5e7eb; padding-top:0.75rem;">
            <strong>Demo Accounts (Password: <code>admin123</code>)</strong><br>
            Manager: <code>manager</code> | Housekeeping: <code>hk_sup</code><br>
            Maintenance: <code>maint_sup</code> | Director: <code>director</code>
        </div>
    </div>
</body>
</html>