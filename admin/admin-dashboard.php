<?php
require_once 'config.php';
require_once 'admin-auth.php';

 $total_passengers = $pdo->query("SELECT COUNT(*) FROM passenger")->fetchColumn();
 $total_trains = $pdo->query("SELECT COUNT(*) FROM train")->fetchColumn();
 $total_schedules = $pdo->query("SELECT COUNT(*) FROM schedule")->fetchColumn();
 $total_tickets = $pdo->query("SELECT COUNT(*) FROM ticket")->fetchColumn();
 $booked_tickets = $pdo->query("SELECT COUNT(*) FROM ticket WHERE Status = 'Booked'")->fetchColumn();
 $total_revenue = $pdo->query("SELECT COALESCE(SUM(Price),0) FROM ticket WHERE Status = 'Booked'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="header-inner">
        <a href="admin-dashboard.php" class="logo" style="text-decoration:none">
            <div class="logo-icon">PR</div>
            <div class="logo-text">
                <span class="main">PAKISTAN RAILWAYS</span>
                <span class="sub">ADMIN PANEL</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="admin-dashboard.php" class="active">Dashboard</a></li>
            <li><a href="manage-passengers.php">Passengers</a></li>
            <li><a href="manage-trains.php">Trains</a></li>
            <li><a href="manage-schedule.php">Schedule</a></li>
            <li><a href="manage-tickets.php">Tickets</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin-logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Admin Dashboard</h2>
        <p>Overview of the Online Metro System</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_passengers; ?></div>
            <div class="stat-label">Total Passengers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_trains; ?></div>
            <div class="stat-label">Total Trains</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_schedules; ?></div>
            <div class="stat-label">Total Schedules</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_tickets; ?></div>
            <div class="stat-label">Total Tickets</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $booked_tickets; ?></div>
            <div class="stat-label">Active Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">PKR <?php echo number_format($total_revenue); ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <div class="section-header" style="margin-top:32px">
        <h2 style="font-size:20px">Quick Actions</h2>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a href="manage-passengers.php" class="btn btn-primary">Manage Passengers</a>
        <a href="manage-trains.php" class="btn btn-primary">Manage Trains</a>
        <a href="manage-schedule.php" class="btn btn-primary">Manage Schedule</a>
        <a href="manage-tickets.php" class="btn btn-primary">Manage Tickets</a>
        <a href="manage-notifications.php" class="btn btn-primary">Send Notifications</a>
    </div>
</div>

</body>
</html>