<?php
function renderHeader(string $title): void {
    $role = $_SESSION['role'] ?? '';
    $fullName = $_SESSION['full_name'] ?? '';
    $current = basename($_SERVER['PHP_SELF']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> | Enkombe Place IMIS</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">
                <h2>ENKOMBE PLACE</h2>
                <span>Hotel & Suites IMIS</span>
            </div>
           <nav>
            <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="unit.php" class="<?= $current === 'unit.php' ? 'active' : '' ?>">Accommodation Units</a>
            <a href="bedroom.php" class="<?= $current === 'bedrooms.php' ? 'active' : '' ?>">Bedroom Status Board</a>
            <?php if (hasRole(['Manager', 'Hotel Manager', 'Housekeeping Supervisor'])): ?>
                <a href="housekeeping.php" class="<?= $current === 'housekeeping.php' ? 'active' : '' ?>">Housekeeping Tasks</a>
                <a href="inspection.php" class="<?= $current === 'inspections.php' ? 'active' : '' ?>">Room Inspections</a>
            <?php endif; ?>
            <?php if (hasRole(['Manager', 'Hotel Manager', 'Maintenance Supervisor'])): ?>
                <a href="maintenance.php" class="<?= $current === 'maintenance.php' ? 'active' : '' ?>">Maintenance Register</a>
            <?php endif; ?>
            <?php if (hasRole(['Manager', 'Hotel Manager', 'Housekeeping Supervisor', 'Maintenance Supervisor'])): ?>
                <a href="staff.php" class="<?= $current === 'staff.php' ? 'active' : '' ?>">Staff & Departments</a>
            <?php endif; ?>
            <a href="reports.php" class="<?= $current === 'reports.php' ? 'active' : '' ?>">Management Reports</a>
        </nav>
            <div class="user-box">
                <strong><?= htmlspecialchars($fullName) ?></strong>
                <small><?= htmlspecialchars($role) ?></small>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <h1><?= htmlspecialchars($title) ?></h1>
                <span class="location-tag">Mbuya, Kampala, Uganda</span>
            </header>
    <?php
}

function renderFooter(): void {
    ?>
        </main>
    </div>
    </body>
    </html>
    <?php
}